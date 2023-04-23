<?php

namespace App\Models;

use App\Events\TaskUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $touches = ['project'];


    protected $casts = [
        'isDone' => 'boolean',
        'dueDate' => 'datetime',
        'completedAt' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'updated' => TaskUpdated::class,
    ];

    public function complete()
    {
        $this->update(['isDone' => true, 'completedAt' => now()]);
    }

    public function incomplete()
    {
        $this->update(['isDone' => false, 'completedAt' => null]);
    }

    public function assignedUser(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Project::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }
}
