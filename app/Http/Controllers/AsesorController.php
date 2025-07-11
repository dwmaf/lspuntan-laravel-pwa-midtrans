<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\Skema;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
class AsesorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.asesor.buatasesor', [
            'asesors' => Asesor::with('skemas')->get(),
            'skemas' => Skema::all()
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required'],
            'role'=>'required',
            'selectedSkemas' => ['required', 'array'],
            'selectedSkemas.*' => ['exists:skemas,id'],
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role'=> $request->role,
        ]);
        $asesor = Asesor::create([
            'user_id'=>$user->id,
            'name' => $request->name,
        ]);
        $asesor->skemas()->attach($request->selectedSkemas);
        return redirect('/asesor')->with('success','Data asesor berhasil ditambah');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asesor $asesor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asesor $asesor)
    {
        $user_asesor = $asesor->user;
        return view('admin.asesor.editasesor', [
            'asesor' => $asesor,
            'user_asesor' => $user_asesor,
            'skemas'=> Skema::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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

        return redirect('/asesor')->with('success','Data asesor berhasil diperbaharui');
    }

    /**
     * Remove the specified resource from storage.
     */
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
