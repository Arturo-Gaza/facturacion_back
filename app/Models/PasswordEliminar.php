<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordEliminar extends Model
{
    use HasFactory;
    protected $table = 'password_eliminar_mail_tokens';
    protected $fillable = ['email', 'codigo', 'created_at', 'used', 'used_at'];
    public $timestamps = false;
}
