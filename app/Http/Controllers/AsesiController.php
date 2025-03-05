<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use App\Models\Sertification;
use Illuminate\Http\Request;

class AsesiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('asesi.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function index_certification()
    // {
    //     return view('asesi.sertifikasi.index',[
    //         'certifications'=>Sertification::all()
    //     ]);
    // }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Asesi $asesi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asesi $asesi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asesi $asesi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asesi $asesi)
    {
        //
    }
}
