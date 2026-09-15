<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;


    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property string|null $telefono
     * @property Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $remember_token
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     */


    #[Fillable([
        'name',
        'email',
        'password',
        'rol',
        'telefono'
    ])]

    #[Hidden([
        'password',
        'remember_token'
    ])]


    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'telefono',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token): void
{
    $this->notify(new CustomResetPassword($token));
}

    public function Cobros()
    {
        return $this->hasMany(Cobros::class, 'User_id');
    }
}