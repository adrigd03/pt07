<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Model;


class Usuari extends Model implements Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;


    protected $table = 'usuaris';
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['nom', 'cognoms', 'username', 'email', 'password', 'admin'];
    protected $hidden = ['password'];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Funcio per comprobar si el nickname existeix 
    public static function isNicknameAvailable($nickname)
    {
        return Usuari::where('username', $nickname)->count() == 0;
    }

    // Funcio per comprobar el nickname y cambiar-lo afegint-li un nombre aleatori
    public static function getAvailableNickname($nickname)
    {
        $newNickname = $nickname;
        $i = 1;
        $usuari = new Usuari();
        while (!$usuari->isNicknameAvailable($newNickname)) {
            $newNickname = $nickname . $i;
            $i++;
        }
        return $newNickname;
    }



    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
    public function getRememberToken()
    {
        return $this->remember_token;
    }
    public function setRememberToken($value)
    {
        $this->remember_token = $value;

    }
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
    public function getAuthIdentifierName()
    {
        return 'email';
    }
    public function getAuthIdentifier()
    {
        return $this->email;
    }
}
