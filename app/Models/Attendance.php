<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'date',
        'status',
        'check_in_at',
        'check_out_at',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }
}

