<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isIntern(): bool
    {
        return $this->role === 'intern';
    }

    public function intern(): HasOne
    {
        return $this->hasOne(Intern::class);
    }

    public function internshipRequests(): HasMany
    {
        return $this->hasMany(InternshipRequest::class);
    }

    public function taskStatuses(): HasMany
    {
        return $this->hasMany(TaskUserStatus::class);
    }
}
