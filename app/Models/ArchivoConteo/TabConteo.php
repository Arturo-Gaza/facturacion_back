<?php

namespace App\Models\ArchivoConteo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabConteo extends Model
{
    use HasFactory;
    protected $table = 'tab_conteos';
    protected $fillable = ['codigo', 'descripcion', 'ume', 'cantidad', 'ubicacion', 'observaciones'];
}
