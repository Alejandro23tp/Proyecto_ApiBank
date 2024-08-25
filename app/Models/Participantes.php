<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participantes extends Model
{
    use HasFactory;
    protected $table = 'participante';
    protected $fillable = [
        'part_nombre', 
        'part_telefono', 
        'part_cupos'
    ];
}
