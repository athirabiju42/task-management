<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_tasks(): void
    {
        $this->get(route('tasks.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_assigned_task(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);
        $task = Task::factory()->create(['assigned_to' => $user->id]);
        $this->actingAs($user)
            ->get(route('tasks.show', $task))
            ->assertOk()
            ->assertSee($task->title);
    }

    public function test_user_cannot_view_unassigned_task(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);
        $task = Task::factory()->create();

        $this->actingAs($user)
            ->get(route('tasks.show', $task))
            ->assertForbidden();
    }

    public function test_admin_can_create_task(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $assignee = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($admin)
            ->post(route('tasks.store'), [
                'title' => 'New Web Task',
                'description' => 'From browser',
                'priority' => 'medium',
                'status' => 'pending',
                'assigned_to' => $assignee->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', ['title' => 'New Web Task']);
    }
}
