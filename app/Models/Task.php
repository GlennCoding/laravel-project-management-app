<?php

namespace App\Models;

use App\Events\TaskUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'dueDate', 'isDone', 'completedAt', 'user_id', 'project_id'
    ];

    protected $casts = [
        'isDone' => 'boolean',
        'dueDate' => 'datetime',
        'completedAt' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'updated' => TaskUpdated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
