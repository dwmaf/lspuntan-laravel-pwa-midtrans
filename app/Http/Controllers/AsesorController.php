<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Http\Requests\StoreAsesorRequest;
use App\Http\Requests\UpdateAsesorRequest;

class AsesorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.asesor.index', [
            'asesors' => Asesor::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.asesor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAsesorRequest $request)
    {
        //
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
        return view('admin.asesor.edit', [
            'asesor' => $asesor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAsesorRequest $request, Asesor $asesor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asesor $asesor)
    {
        //
    }
}
