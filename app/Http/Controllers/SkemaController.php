<?php

namespace App\Http\Controllers;

use App\Models\Skema;
use App\Http\Requests\StoreSkemaRequest;
use App\Http\Requests\UpdateSkemaRequest;
use Illuminate\Http\Request;

class SkemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.skema.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.skema.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validatedData=$request->validate([
            'nama_skema' => ['required', 'string', 'max:255'],
        ]);
        Skema::create($validatedData);
        return redirect(route('dashboardadmin'))->with('Success','Data Skema berhasil ditambah');
    }

    /**
     * Display the specified resource.
     */
    public function show(Skema $skema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skema $skema)
    {
        return view('admin.skema.edit',[
            'skema'=>$skema
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skema $skema)
    {
        $validatedData = $request->validate([
            'nama_skema' => ['required', 'string','max:255'],
        ]);
        // if($request->file('link_foto')) {
        //     if($request->oldImage){
        //         Storage::delete($request->oldImage);
        //     }
        //     $validatedData['link_foto'] = $request->file('link_foto')->store('berita-images');
        // }
        Skema::where('id', $skema->id)
            ->update($validatedData);

            return redirect(route('dashboardadmin'))->with('Success','Data Skema berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skema $skema)
    {
        // if($skema->link_foto){
        //     Storage::delete($skema->link_foto);
        // }
        Skema::destroy($skema->id);
        return redirect(route('dashboardadmin'))->with('success', 'Skema berhasil dihapus');
    }
}
