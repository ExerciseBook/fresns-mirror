<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function scopeOfAdmin($query)
    {
        return $query->where('user_type', 1);
    }

    public function getSecretPurePhoneAttribute(): string
    {
        if (!$this->pure_phone) {
            return '';
        }
        return \Str::mask($this->pure_phone, '*', -8, 4);
    }

    public function getSecretEmailAttribute(): string
    {
        if (!$this->email) {
            return '';
        }

        list($prefix, $end) = explode('@', $this->email);
        $len = ceil(strlen($prefix) / 2);
        return \Str::mask($prefix, '*', -1 * $len, $len) .'@'. $end;
    }

    public function isAdmin()
    {
        return $this->user_type == 1;
    }
}
