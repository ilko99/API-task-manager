<?php

namespace Tests\Feature\CommentController;

use Tests\TestCase;
use App\Models\Task;
use App\Models\Project;
use Laravel\Sanctum\Sanctum;

class CommentStoreTest extends TestCase
{
    public function test_create_comments_for_tasks(): void
    {
        $task = Task::factory()->create();
        Sanctum::actingAs($task->creator);

        $route = route('tasks.comments.store', $task);
        $response = $this->postJson($route, [
            'content' => 'foo',
        ]);

        
        $response->assertCreated();

        $this->assertDatabaseHas('comments',[
            'content' => 'foo',
            "user_id" => $task->creator_id,
            'commentable_id' => $task->id,
            'commentable_type' => Task::class,
        ]);
    }

    public function test_create_comments_for_projects(): void
    {
        $project = Project::factory()->create();
        Sanctum::actingAs($project->creator);

        $route = route('projects.comments.store', $project);
        $response = $this->postJson($route, [
            'content' => 'bar',
        ]);

        
        $response->assertCreated();

        $this->assertDatabaseHas('comments',[
            'content' => 'bar',
            "user_id" => $project->creator_id,
            'commentable_id' => $project->id,
            'commentable_type' => Project::class,
        ]);
    }

}

