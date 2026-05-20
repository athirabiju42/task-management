<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_only_assigned_tasks_via_api(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);
        $other = User::factory()->create(['role' => UserRole::User]);

        Task::factory()->create(['assigned_to' => $user->id, 'title' => 'Mine']);
        Task::factory()->create(['assigned_to' => $other->id, 'title' => 'Not mine']);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Mine');
    }

    public function test_admin_can_create_task_via_api(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $assignee = User::factory()->create(['role' => UserRole::User]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/tasks', [
            'title' => 'API Task',
            'description' => 'Created from API',
            'priority' => 'high',
            'assigned_to' => $assignee->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'API Task')
            ->assertJsonPath('data.ai_summary', fn ($value) => ! empty($value));

        $this->assertDatabaseHas('tasks', ['title' => 'API Task']);
    }

    public function test_user_can_update_task_status_via_api(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);
        $task = Task::factory()->create([
            'assigned_to' => $user->id,
            'status' => TaskStatus::Pending,
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => TaskStatus::InProgress->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', TaskStatus::InProgress->value);
    }

    public function test_user_cannot_update_other_users_task_status(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);
        $other = User::factory()->create(['role' => UserRole::User]);
        $task = Task::factory()->create(['assigned_to' => $other->id]);

        Sanctum::actingAs($user);

        $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => TaskStatus::Completed->value,
        ])->assertForbidden();
    }

    public function test_ai_summary_endpoint_returns_summary(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);
        $task = Task::factory()->create([
            'assigned_to' => $user->id,
            'ai_summary' => 'Existing summary',
            'ai_priority' => 'medium',
        ]);

        Sanctum::actingAs($user);

        $this->getJson("/api/tasks/{$task->id}/ai-summary")
            ->assertOk()
            ->assertJsonPath('data.ai_summary', 'Existing summary');
    }
}
