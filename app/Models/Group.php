<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'filiere',
        'department',
        'max_interns',
        'days_of_week',
        'color',
        'description',
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];



    public function interns(): HasMany
    {
        return $this->hasMany(Intern::class);
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function activeInterns(): HasMany
    {
        return $this->interns()->active();
    }

    public function currentInterns(): HasMany
    {
        return $this->interns()->current();
    }

    public static function forFiliere(string $filiere): self
    {
        $normalized = trim($filiere) !== '' ? trim($filiere) : 'General';

        return static::firstOrCreate(
            ['filiere' => $normalized],
            [
                'name'          => Str::title($normalized),
                'max_interns'   => 100, // generous default, since grouping is now auto by filiere
            ]
        );
    }
}
