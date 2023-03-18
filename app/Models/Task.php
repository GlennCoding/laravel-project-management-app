<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'dueDate', 'isDone'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
