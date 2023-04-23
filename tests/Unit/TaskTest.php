<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    function it_belongs_to_a_project()
    {
        $task = Task::factory()->create();

        $this->assertInstanceOf(Project::class, $task->project);
    }

    /** @test */
    function it_has_a_path()
    {
        $task = Task::factory()->create();

        $this->assertEquals("/projects/{$task->project->id}/tasks/{$task->id}", $task->path());
    }

    /** @test */
    function it_can_be_completed()
    {
        $this->withoutExceptionHandling();

        $task = Task::factory()->create();

        $this->assertFalse($task->isDone);

        $task->complete();

        $this->assertTrue($task->fresh()->isDone);
        $this->assertNotNull($task->completedAt);
    }

    /** @test */
    function it_can_be_marked_as_incomplete()
    {
        $task = Task::factory()->create(['isDone' => true]);

        $this->assertTrue($task->isDone);

        $task->incomplete();

        $this->assertFalse($task->fresh()->isDone);
        $this->assertNull($task->completedAt);
    }
}
