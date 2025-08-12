<?php

namespace App\Http\Controllers;

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
class AsesorController extends Controller
{
    // buat nampilin daftar asesor sekaligus untuk nambah asesor
    public function index()
    {
        return view('admin.asesor.buatasesor', [
            'asesors' => Asesor::with('skemas')->withCount('sertifications')->get(),
            'skemas' => Skema::all()
        ]);
    }


    // buat nyimpan asesor baru sekaligus ngirim email ke mereka buat password baru untuk akun mereka
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'selectedSkemas' => ['required', 'array'],
            'selectedSkemas.*' => ['exists:skemas,id'],
        ]);
        DB::transaction(function () use ($request) {
            // 1. Buat user baru dengan password acak yang sangat aman
            $user = User::create([
                'email' => $request->email,
                // Password ini tidak akan pernah digunakan oleh user, hanya sebagai placeholder
                'password' => Hash::make(Str::random(16)), 
            ]);

            // 2. Beri peran 'asesor'
            $user->assignRole('asesor');

            // 3. Buat data asesor
            $asesor = Asesor::create([
                'user_id' => $user->id,
                'name' => $request->name,
            ]);

            // 4. Hubungkan asesor dengan skema yang dipilih
            $asesor->skemas()->attach($request->selectedSkemas);

            // 5. Tandai email sebagai terverifikasi & kirim notifikasi setup password
            $user->markEmailAsVerified(); // <-- Tandai email sudah verified
            $user->notify(new AsesorAccountCreated()); // <-- Kirim notifikasi baru
        });
        
        return redirect('/admin/asesor')->with('success','Data asesor berhasil ditambah, Asesor akan menerima Email untuk buat password');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asesor $asesor)
    {
        //
    }

    // buat mengedit akun asesor mereka
    public function edit(Asesor $asesor)
    {
        $user_asesor = $asesor->user;
        return view('admin.asesor.editasesor', [
            'asesor' => $asesor,
            'user_asesor' => $user_asesor,
            'skemas'=> Skema::all()
        ]);
    }

    // buat mengupdate akun asesor mereka yg udh diedit
    public function update(Request $request, Asesor $asesor)
    {
        $user_asesor = $asesor->user;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user_asesor->id],
            'password' => ['nullable'],
            'selectedSkemas' => ['required', 'array'],
            'selectedSkemas.*' => ['exists:skemas,id'],
        ]);
        $asesorData = ['name'=>$request->name];
        $userData = ['email'=>$request->email];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $asesor->update($asesorData);
        $user_asesor->update($userData);
        $asesor->skemas()->sync($request->selectedSkemas);

        return redirect('/admin/asesor')->with('success','Data asesor berhasil diperbaharui');
    }

    // buat menghapus akun asesor
    public function destroy(Asesor $asesor)
    {
        $user = $asesor->user;
        // dd($user);
        $asesor->skemas()->detach();
        $asesor->delete();
        if($user) {
            $user->delete();
        }
        return redirect('/asesor')->with('success','Data asesor berhasil dihapus');
    }
}
