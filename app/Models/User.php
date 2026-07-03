<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Gebruikt de bestaande users-tabel uit kniploket_tiko.
     */
    protected $table = 'users';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'IsActief' => 'boolean',
        ];
    }

    /**
     * Controleert of gebruiker de rol eigenaar heeft.
     */
    public function isEigenaar(): bool
    {
        return $this->role === 'eigenaar';
    }

    /**
     * Controleert of het account actief is.
     */
    public function isActief(): bool
    {
        return (bool) $this->IsActief;
    }
}
