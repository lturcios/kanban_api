<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tablero extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre']; // Campos asignables masivamente

    /*
    * Relación uno a muchos con Columna
    */
    public function columnas()
    {
        return $this->hasMany(Columna::class)->orderBy('posicion');
    }
}
