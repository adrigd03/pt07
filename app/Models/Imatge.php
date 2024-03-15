<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imatge extends Model
{
    use HasFactory;

    protected $table = 'imatges';
    protected $fillable = ['titol', 'descripcio', 'url','usuari'];
    protected $primaryKey = 'id';
    public function usuari()
    {
        return $this->belongsTo(User::class, 'usuari', 'email');
    }
}
