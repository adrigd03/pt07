<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['titol', 'contingut', 'image', 'usuari'];

    public function usuari()
    {
        return $this->belongsTo(Usuari::class, 'usuari', 'email');
    }

  


}
