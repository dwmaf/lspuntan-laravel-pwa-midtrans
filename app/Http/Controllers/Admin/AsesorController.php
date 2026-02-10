<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asesor;
use App\Models\Skema;
use App\Models\User;
use App\Exports\AsesorExport;
use App\Notifications\AsesorAccountCreated;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AsesorController extends Controller
{
    public function index(Request $request)
    {
        $asesors = Asesor::query()
            ->with('skemas', 'user')
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('user',function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->input('skema'), function ($query, $skema) {
                $query->whereHas('skemas',function ($q) use ($skema) {
                    $q->where('skemas.id', $skema);
                });
            })
            ->when($request->input('role'), function ($query, $role) {
                $query->whereHas('roles', fn($q) => $q->where('name', $role));
            })
            ->when($request->input('status'), function ($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->withCount('sertifications')
            ->latest()
            ->paginate(15)
            ->onEachSide(0)
            ->withQueryString();
        return Inertia::render('Admin/AsesorAdmin', [
            'asesors' => $asesors,
            'skemas' => Skema::all(),
            'filters' => $request->only(['skema','search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'no_tlp_hp' => 'required|string|max:255',
            'no_met' => 'required|string|max:255',
            'masa_berlaku_sertif_teknis' => 'required|date',
            'masa_berlaku_sertif_asesor' => 'required|date',
            'selectedSkemas' => ['required', 'array'],
            'selectedSkemas.*' => ['exists:skemas,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'no_tlp_hp' => $request->no_tlp_hp,
                'password' => Hash::make(Str::random(16)),
            ]);
            $user->assignRole('asesor');
            $asesor = Asesor::create([
                'user_id' => $user->id,
                'no_reg_met' => $request->no_met,
                'masa_berlaku_sertif_teknis' => $request->masa_berlaku_sertif_teknis,
                'masa_berlaku_sertif_asesor' => $request->masa_berlaku_sertif_asesor,
                'is_active' => $request->boolean('is_active', true)
            ]);
            $asesor->skemas()->attach($request->selectedSkemas);
            $user->markEmailAsVerified();
            // uncomment kalo udh ada mail host nya
            // $user->notify(new AsesorAccountCreated());
        });

        return redirect(route('admin.asesor.index'))->with('message', 'Data asesor berhasil ditambah, Asesor akan menerima Email untuk buat password');
    }

    public function update(Asesor $asesor, Request $request)
    {
        $user_asesor = $asesor->user;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user_asesor->id],
            'no_tlp_hp' => 'required|string|max:255',
            'no_met' => 'required|string|max:255',
            'masa_berlaku_sertif_teknis' => 'required|date',
            'masa_berlaku_sertif_asesor' => 'required|date',
            'password' => ['nullable'],
            'selectedSkemas' => ['required', 'array'],
            'selectedSkemas.*' => ['exists:skemas,id'],
            'is_active' => ['nullable', 'boolean'], // Tambahan status
        ]);

        // Cek skema yang akan dihapus
        $currentSkemas = $asesor->skemas->pluck('id')->toArray();
        $newSkemas = $request->selectedSkemas;
        $removedSkemas = array_diff($currentSkemas, $newSkemas);

        // Validasi: cek apakah ada sertifikasi dengan skema yang akan dihapus
        if (!empty($removedSkemas)) {
            $activeSertifications = $asesor->sertifications()
                ->whereIn('skema_id', $removedSkemas)
                ->whereIn('status', ['berlangsung'])
                ->with('skema')
                ->get();

            if ($activeSertifications->isNotEmpty()) {
                $skemaNames = $activeSertifications->pluck('skema.nama_skema')->unique()->implode(', ');
                return back()->withErrors([
                    'selectedSkemas' => "Tidak dapat menghapus skema: {$skemaNames}. Asesor masih ditugaskan pada sertifikasi yang sedang berlangsung dengan skema tersebut."
                ]);
            }
        }

        if ($asesor->is_active && $request->boolean('is_active') === false) {
            $isBusy = $asesor->sertifications()
                ->where('status', 'berlangsung')
                ->exists();

            if ($isBusy) {
                return back()->withErrors([
                    'is_active' => 'Asesor tidak dapat dinonaktifkan karena masih terlibat dalam sertifikasi yang sedang berlangsung.'
                ]);
            }
        }

        DB::transaction(function () use ($request, $asesor, $user_asesor) {
            $userData = [
                'email' => $request->email,
                'no_tlp_hp' => $request->no_tlp_hp,
                'name' => $request->name
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user_asesor->update($userData);
            $asesor->update([
                'no_reg_met' => $request->no_met,
                'masa_berlaku_sertif_teknis' => $request->masa_berlaku_sertif_teknis,
                'masa_berlaku_sertif_asesor' => $request->masa_berlaku_sertif_asesor,
                'is_active' => $request->boolean('is_active'),
            ]);
            $asesor->skemas()->sync($request->selectedSkemas);
        });

        return redirect(route('admin.asesor.index'))->with('message', 'Data asesor berhasil diperbaharui');
    }

    public function destroy(Asesor $asesor)
    {
        $user = $asesor->user;
        
        if ($user && $user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        if ($asesor->sertifications()->exists() || $asesor->plotingans()->exists()) {
             return redirect(route('admin.asesor.index'))->with('error', 'Asesor tidak bisa dihapus karena memiliki riwayat sertifikasi. Silakan non-aktifkan statusnya di menu Edit.');
        }

        DB::transaction(function () use ($asesor, $user) {
            $asesor->skemas()->detach();
            $asesor->delete();
            if ($user) {
                $user->delete();
            }
        });
        return redirect(route('admin.asesor.index'))->with('message', 'Data asesor berhasil dinonaktifkan (User telah di-banned)');
    }

    public function export()
    {
        return Excel::download(new AsesorExport, 'Laporan_Asesor_LSP_Untan.xlsx');
    }
}
