<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordCambiarPhone extends Model
{
    use HasFactory;
    protected $table = 'password_cambiar_phone_tokens';
    protected $fillable = ['phone', 'codigo', 'created_at', 'used', 'used_at'];
    public $timestamps = false;
}
