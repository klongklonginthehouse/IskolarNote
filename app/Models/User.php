<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'credits'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Moderators, faculty and admins can open the review queue. */
    public function isStaff(): bool
    {
        return in_array($this->role, ['moderator', 'faculty', 'admin'], true);
    }

    public function canUpload(): bool
    {
        return in_array($this->role, ['author', 'moderator', 'faculty', 'admin'], true);
    }

    /** Badge tiers: reputation badge system from the proposal. */
    public function badge(): ?string
    {
        $n = $this->resources()->where('status', 'verified')->where('is_official', false)->count();

        return match (true) {
            $n >= 5 => 'Iskolar ng Bayan',
            $n >= 3 => "Dean's Lister",
            $n >= 1 => 'First Contributor',
            default => null,
        };
    }

    public function reputation(): int
    {
        $q = $this->resources()->where('status', 'verified')->where('is_official', false);

        return (int) $q->sum('votes_count') + ($q->count() * 10);
    }
}
