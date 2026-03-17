<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'issue_date',
        'hours_completed',
        'projects',
        'soft_skills',
        'signed_by',
        'notes',
        'message',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function intern()
    {
        return $this->belongsTo(Intern::class);
    }
}
