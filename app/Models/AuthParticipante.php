<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class AuthParticipante extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'email',
        'username',
        'password',
        'participante_id'
    ];
    protected $hidden = ['password', 'remember_token'];

    public function participante()
    {
        return $this->belongsTo(Participantes::class, 'participante_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
