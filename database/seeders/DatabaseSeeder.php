<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@taskmanager.test',
            'role' => UserRole::Admin,
        ]);

        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@taskmanager.test',
            'role' => UserRole::User,
        ]);

        // $sampleTasks = [
        //     [
        //         'title' => 'Prepare quarterly report',
        //         'description' => 'Compile Q1 metrics and draft executive summary for leadership review.',
        //         'priority' => TaskPriority::High,
        //         'status' => TaskStatus::InProgress,
        //         'due_date' => now()->addDays(5),
        //         'assigned_to' => $user->id,
        //     ],
        //     [
        //         'title' => 'Update team documentation',
        //         'description' => 'Refresh onboarding docs and API reference for new hires.',
        //         'priority' => TaskPriority::Medium,
        //         'status' => TaskStatus::Pending,
        //         'due_date' => now()->addDays(12),
        //         'assigned_to' => $user->id,
        //     ],
        //     [
        //         'title' => 'Review pull requests',
        //         'description' => 'Code review for backend feature branch and provide feedback.',
        //         'priority' => TaskPriority::Low,
        //         'status' => TaskStatus::Completed,
        //         'due_date' => now()->subDay(),
        //         'assigned_to' => $admin->id,
        //     ],
        // ];

        // foreach ($sampleTasks as $data) {
        //     Task::create($data);
        // }

        // Task::factory()->count(7)->create([
        //     'assigned_to' => $user->id,
        // ]);
    }
}
