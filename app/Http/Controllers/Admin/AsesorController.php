<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asesor;
use App\Models\Skema;
use App\Models\User;
use App\Notifications\AsesorAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

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
            ->withCount('sertifications')
            ->latest()
            ->paginate(15)
            ->onEachSide(0)
            ->withQueryString();
        return Inertia::render('Admin/AsesorAdmin', [
            'asesors' => $asesors,
            'skemas' => Skema::all(),
            'filters' => $request->only(['skema','search']),
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'no_tlp_hp' => 'required|string|max:255',
            'masa_berlaku_sertif_teknis' => 'required|date',
            'masa_berlaku_sertif_asesor' => 'required|date',
            'selectedSkemas' => ['required', 'array'],
            'selectedSkemas.*' => ['exists:skemas,id'],
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
                'masa_berlaku_sertif_teknis' => $request->masa_berlaku_sertif_teknis,
                'masa_berlaku_sertif_asesor' => $request->masa_berlaku_sertif_asesor,
            ]);
            $asesor->skemas()->attach($request->selectedSkemas);
            $user->markEmailAsVerified();
            // uncomment kalo udh ada mail host nya
            // $user->notify(new AsesorAccountCreated());
        });

        return redirect(route('admin.asesor.index'))->with('message', 'Data asesor berhasil ditambah, Asesor akan menerima Email untuk buat password');
    }

    public function update($asesor_id, Request $request)
    {
        $asesor = Asesor::findOrFail($asesor_id);
        $user_asesor = $asesor->user;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user_asesor->id],
            'no_tlp_hp' => 'required|string|max:255',
            'masa_berlaku_sertif_teknis' => 'required|date',
            'masa_berlaku_sertif_asesor' => 'required|date',
            'password' => ['nullable'],
            'selectedSkemas' => ['required', 'array'],
            'selectedSkemas.*' => ['exists:skemas,id'],
        ]);
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
                'masa_berlaku_sertif_teknis' => $request->masa_berlaku_sertif_teknis,
                'masa_berlaku_sertif_asesor' => $request->masa_berlaku_sertif_asesor
            ]);
            $asesor->skemas()->sync($request->selectedSkemas);
        });

        return redirect(route('admin.asesor.index'))->with('message', 'Data asesor berhasil diperbaharui');
    }

    public function destroy($asesor_id)
    {
        $asesor = Asesor::findOrFail($asesor_id);
        $user = $asesor->user;
        DB::transaction(function () use ($asesor, $user) {
            $asesor->skemas()->detach();
            $asesor->delete();
            if ($user) {
                $user->delete();
            }
        });
        return redirect(route('admin.asesor.index'))->with('message', 'Data asesor berhasil dihapus');
    }
}
