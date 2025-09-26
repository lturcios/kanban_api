<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea; 
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tarea::query()->orderBy('posicion');

        if ($request->has('columna_id')) {
            $query->where('columna_id', $request->query('columna_id'));
        }

        $tareas = $query->get();

        return response()->json($tareas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación acorde a la migración
        $request->validate([
            'columna_id' => 'required|exists:columnas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'fecha_asignacion' => 'required|date',
            'fecha_limite' => 'required|date|after_or_equal:fecha_asignacion',
            'usuario_asignador' => 'required|string|max:255',
            'usuario_asignado' => 'required|string|max:255',
            'avance' => 'sometimes|integer|min:0|max:100',
            'prioridad' => 'sometimes|in:baja,media,alta',
            'posicion' => 'sometimes|integer|min:0',
        ]);

        $fillable = (new Tarea())->getFillable();
        $data = $request->only($fillable);

        DB::transaction(function () use (&$tarea, $data) {
            // Calcular posicion si no viene: poner al final de la columna
            if (!array_key_exists('posicion', $data) || is_null($data['posicion'])) {
                $maxPos = Tarea::where('columna_id', $data['columna_id'])->max('posicion');
                $data['posicion'] = is_null($maxPos) ? 0 : $maxPos + 1;
            } else {
                // abrir espacio en la columna destino
                Tarea::where('columna_id', $data['columna_id'])
                    ->where('posicion', '>=', $data['posicion'])
                    ->increment('posicion');
            }

            $tarea = Tarea::create($data);
        });

        return response()->json($tarea, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tarea $tarea)
    {
        return response()->json($tarea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tarea $tarea)
    {
        $rules = [
            'columna_id' => 'sometimes|exists:columnas,id',
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|nullable|string|max:255',
            'fecha_asignacion' => 'sometimes|date',
            'fecha_limite' => 'sometimes|date|after_or_equal:fecha_asignacion',
            'usuario_asignador' => 'sometimes|string|max:255',
            'usuario_asignado' => 'sometimes|string|max:255',
            'avance' => 'sometimes|integer|min:0|max:100',
            'prioridad' => 'sometimes|in:baja,media,alta',
            'posicion' => 'sometimes|integer|min:0',
        ];

        $request->validate($rules);

        $fillable = (new Tarea())->getFillable();
        $data = $request->only($fillable);

        // No reordenamos aquí — use move() para cambios de columna/posición
        $tarea->update($data);

        return response()->json($tarea);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarea $tarea)
    {
        DB::transaction(function () use ($tarea) {
            $columnaId = $tarea->columna_id;
            $pos = $tarea->posicion;

            $tarea->delete();

            Tarea::where('columna_id', $columnaId)
                ->where('posicion', '>', $pos)
                ->decrement('posicion');
        });

        return response()->json(null, Response::HTTP_NO_CONTENT);
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

    /**
     * Mover / reordenar tarea.
     * Ajusta posiciones de tareas afectadas dentro de transacción.
     */
    public function move(Request $request, Tarea $tarea)
    {
        $request->validate([
            'columna_id' => 'sometimes|exists:columnas,id',
            'posicion' => 'sometimes|nullable|integer|min:0',
        ]);

        $newColumnaId = $request->input('columna_id', $tarea->columna_id);
        $newPos = $request->input('posicion');

        DB::transaction(function () use ($tarea, $newColumnaId, &$newPos) {
            $oldColumnaId = $tarea->columna_id;
            $oldPos = $tarea->posicion;

            // Si no envían posicion, poner al final en columna destino
            if (is_null($newPos)) {
                $maxPos = Tarea::where('columna_id', $newColumnaId)->max('posicion');
                $newPos = is_null($maxPos) ? 0 : $maxPos + 1;
            }

            if ($newColumnaId != $oldColumnaId) {
                // Compactar columna origen (reducir posiciones > oldPos)
                Tarea::where('columna_id', $oldColumnaId)
                    ->where('posicion', '>', $oldPos)
                    ->decrement('posicion');

                // Abrir espacio en columna destino (incrementar >= newPos)
                Tarea::where('columna_id', $newColumnaId)
                    ->where('posicion', '>=', $newPos)
                    ->increment('posicion');

                $tarea->columna_id = $newColumnaId;
                $tarea->posicion = $newPos;
                $tarea->save();
            } else {
                // Reordenamiento dentro de misma columna
                if ($newPos == $oldPos) {
                    return;
                }

                if ($newPos < $oldPos) {
                    Tarea::where('columna_id', $oldColumnaId)
                        ->whereBetween('posicion', [$newPos, $oldPos - 1])
                        ->increment('posicion');
                } else {
                    Tarea::where('columna_id', $oldColumnaId)
                        ->whereBetween('posicion', [$oldPos + 1, $newPos])
                        ->decrement('posicion');
                }

                $tarea->posicion = $newPos;
                $tarea->save();
            }
        });

        $tarea->refresh();
        return response()->json($tarea);
    }
}
