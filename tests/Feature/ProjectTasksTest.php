<?php

namespace Tests\Feature;

use App\Enums\UserProjectRoleEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Factories\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function guests_cannot_add_tasks_to_projects()
    {
        $project = Project::factory()->create();

        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

    /** @test */
    public function only_the_owner_of_a_project_may_add_tasks()
    {
        $this->signIn();

        $project = Project::factory()->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }

    /** @test */
    public function only_the_owner_of_a_project_may_update_a_task()
    {
        $this->signIn();

        $project = Project::factory()->withTasks(1)->create();

        $this->patch($project->tasks[0]->path(), ['body' => 'changed'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        $this->withoutExceptionHandling();

        $project = Project::factory()->create();

        $attributes = ['body' => 'Test task', 'dueDate' => '2023-08-22 00:00:00'];

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $attributes);

        $this->assertDatabaseHas('tasks', $attributes);
    }

    /** @test */
    public function a_task_can_be_deleted(): void
    {
        $project = Project::factory()->withTasks(1)->create();
        $task = $project->tasks[0];

        $this->actingAs($project->owner)->delete($task->path())->assertRedirect($project->path());

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    function a_task_can_be_updated()
    {
        $project = Project::factory()->withTasks(1)->create();

        $newAttributes = ['body' => 'changed', 'dueDate' => '2023-08-22 00:00:00'];

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), $newAttributes);

        $this->assertDatabaseHas('tasks', $newAttributes);
    }

    /** @test */
    function a_task_can_be_marked_as_complete()
    {
        $this->withoutExceptionHandling();

        $project = Project::factory()->withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'isDone' => true
            ]);

        $this->assertDatabaseHas('tasks', [
            'isDone' => 1
        ]);
    }

    /** @test */
    function a_task_can_be_marked_as_incomplete()
    {
        $this->withoutExceptionHandling();

        $project = Project::factory()->withTasks(1)->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'isDone' => true
            ]);

        $this->patch($project->tasks[0]->path(), [
            'isDone' => false
        ]);

        $this->assertDatabaseHas('tasks', [
            'isDone' => false
        ]);
    }

    /** @test */
    public function a_task_requires_a_body()
    {
        $project = Project::factory()->create();

        $attributes = Task::factory()->raw(['body' => '']);

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $attributes)
            ->assertSessionHasErrors('body');
    }
}
