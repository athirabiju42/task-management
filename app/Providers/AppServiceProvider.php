<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;
use App\View\Composers\SidebarStatsComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);

        Gate::define('admin', fn (User $user) => $user->isAdmin());

        View::composer(
            ['layouts.task', 'tasks.*', 'dashboard'],
            SidebarStatsComposer::class
        );
    }
}
