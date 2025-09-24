<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;
    protected $fillable = [
        'columna_id', 'nombre', 'descripcion', 
        'fecha_asignacion', 'fecha_limite', 
        'usuario_asignador', 'usuario_asignado', 
        'avance', 'prioridad', 'posicion'
    ];

    /* 
     * Definimos campos que deben ser casteados a fechas
     */
    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_limite' => 'date',
    ];

    /*
     * Relación inversa con Columna
     */
    public function columna()
    {
        return $this->belongsTo(Columna::class);
    }
}
