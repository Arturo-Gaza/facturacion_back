<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatRoles extends Model
{
    use HasFactory;
    protected $table = 'cat_roles';
    protected $fillable = ['nombre','habilitado','recupera_gastos'.'consola'];
}


