<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ranking extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'attendance_score',
        'activity_score',
        'admin_note_score',
        'total_score',
        'rank_position',
        'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
    ];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }
}
