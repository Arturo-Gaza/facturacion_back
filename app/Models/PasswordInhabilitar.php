<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordInhabilitar extends Model
{
    use HasFactory;
    protected $table = 'password_inhabilitar_mail_tokens';
    protected $fillable = ['email', 'codigo', 'created_at', 'used', 'used_at'];
    public $timestamps = false;
}
