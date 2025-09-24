<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Actualiza el orden de las tareas dentro de una columna.
     */
    public function actualizarOrden(Request $request)
    {
        $validated = $request->validate([
            'columna_id' => 'required|exists:columnas,id',
            'tareas' => 'required|array',
            'tareas.*.id' => 'required|integer|exists:tareas,id',
            'tareas.*.posicion' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['tareas'] as $index => $tareaId) {
                DB::table('tareas')
                    ->where('id', $tareaId)
                    ->update([
                        'posicion' => $index
                    
                    ]);
            }
        });

        return response()->json(['message' => 'Orden de tareas actualizado correctamente.']);
    }
}
