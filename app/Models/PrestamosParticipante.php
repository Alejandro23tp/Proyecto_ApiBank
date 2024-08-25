<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamosParticipante extends Model
{
    use HasFactory;

    protected $table = 'prestamos_participante';
    protected $fillable = [
        'pp_partId',
        'pp_semana',
        'pp_prestamo',
        'interes',
        'estado',
        'fecha_pago',
    ];
}
