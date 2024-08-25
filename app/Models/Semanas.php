<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semanas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_semana ',
        'part_id',
        'feccha_pago',
        'valor',
        'responsable',
        'inicioSemana'
    ];

    
}
