<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Columna extends Model
{
    use HasFactory;
    protected $fillable = ['tablero_id', 'titulo', 'posicion', 'color'];


    /*    
    * Relación inversa con Tablero
    */
    public function tablero()
    {
        return $this->belongsTo(Tablero::class);
    }

    /*
    * Relación uno a muchos con Tarea
    */
    public function tareas()
    {
        return $this->hasMany(Tarea::class)->orderBy('posicion');
    }
}
