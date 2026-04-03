<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intern extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'internship_request_id',
        'department',
        'group_id',
        'start_date',
        'end_date',
        'duration_days',
        'active',
        'admin_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(InternshipRequest::class, 'internship_request_id');
    }



    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)
            ->where(function ($sub) {
                $sub->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            });
    }

    /**
     * Current = explicitly active OR end_date still in the future.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('active', true)
              ->orWhere('end_date', '>', now()->toDateString());
        });
    }
}
