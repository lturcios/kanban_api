<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tablero;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TableroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Tablero::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $tablero = Tablero::create($validated);
        return response()->json($tablero, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tablero $tablero)
    {
        /*
        * Cargar las columnas y las tareas relacionadas con el tablero
        * para devolver una estructura completa del tablero.
        */
        return $tablero->load('columnas.tareas');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tablero $tablero)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $tablero->update($validated);
        return response()->json($tablero);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tablero $tablero)
    {
        $tablero->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
