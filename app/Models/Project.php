<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function path()
    {
        return "/projects/{$this->id}";
    }

    public function addTask($body, $dueDate)
    {
        return $this->tasks()->create([
            'body' => $body,
            'dueDate' => $dueDate,
            'assigned_user_id' => $this->owner_id
        ]);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function invite(User $member)
    {
        $this->members()->attach($member);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_member')
            ->withTimestamps();
    }

    public function leave(User $member)
    {
        $this->members()->detach($member);
    }
}
