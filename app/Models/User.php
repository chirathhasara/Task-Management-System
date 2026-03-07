<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function findByEmail(string $email): ?User
    {
        return static::where('email', $email)->first();
    }

    public static function createUser(array $data): User
    {
        return static::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
        ]);
    }

    public function verifyPassword(string $password): bool
    {
        return \Illuminate\Support\Facades\Hash::check($password, $this->password);
    }

    public function generateToken(string $tokenName = 'auth_token'): string
    {
        return $this->createToken($tokenName)->plainTextToken;
    }

    public function revokeAllTokens(): void
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        foreach ($this->tokens as $token) {
            $token->delete();
        }
    }

    public function revokeCurrentToken(): void
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
        if ($token = $this->currentAccessToken()) {
            $token->delete();
        }
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
