<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'profile_image',
        'status',
        'suspended_at',
        'suspension_reason',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function assignRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        if (!$this->roles->contains($role)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $this->roles()->detach($role);
        }
    }

    public function vetProfile(): HasOne
    {
        return $this->hasOne(VetProfile::class);
    }

    public function shelterProfile(): HasOne
    {
        return $this->hasOne(ShelterProfile::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'owner_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'owner_id');
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'vet_specializations', 'vet_id', 'specialization_id');
    }
}
