<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    protected $table = 'password_reset_tokens';
    protected $fillable = ['email', 'codigo', 'created_at', 'used', 'used_at'];
    public $timestamps = false;
}
