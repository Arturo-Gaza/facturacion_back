<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model
{
    use HasFactory;

    protected $table = 'user_phones';

    protected $fillable = [
        'user_id',
        'telefono',
        'verificado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
