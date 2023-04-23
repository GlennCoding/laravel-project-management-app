<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function it_has_a_path()
    {
        $project = Project::factory()->create();

        $this->assertEquals('/projects/' . $project->id, $project->path());
    }

    /** @test */
    public function it_belongs_to_an_owner()
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf(User::class, $project->owner);
    }

    /** @test */
    public function it_can_add_a_task()
    {
        $this->withoutExceptionHandling();

        $project = Project::factory()->create();

        $task = $project->addTask('Test task', null);

        $this->assertCount(1, $project->tasks);
        $this->assertTrue($project->tasks->contains($task));
    }

    /** @test */
    function it_can_invite_a_user()
    {
        $project = Project::factory()->create();

        $project->invite($user = User::factory()->create());

        $this->assertTrue($project->members->contains($user));
    }

    /** @test */
    function it_can_leave_as_member()
    {
        $project = Project::factory()->create();
        $project->members()->attach($user = User::factory()->create());

        $project->leave($user);

        $this->assertTrue($project->members->doesntContain($user));
    }
}
