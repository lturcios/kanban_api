<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Columna;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ColumnaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Columna::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tablero_id' => 'required|exists:tableros,id',
            'orden' => 'required|integer',
        ]);
        $columna = Columna::create($validated);
        return response()->json($columna, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Columna $columna)
    {
        return $columna->load('tareas');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Columna $columna)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tablero_id' => 'required|exists:tableros,id',
            'orden' => 'required|integer',
        ]);
        $columna->update($validated);
        return response()->json($columna);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Columna $columna)
    {
        $columna->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
