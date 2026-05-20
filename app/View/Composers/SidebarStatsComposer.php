<?php

namespace App\View\Composers;

use App\Services\TaskService;
use Illuminate\View\View;

class SidebarStatsComposer
{
    public function __construct(protected TaskService $taskService) {}

    public function compose(View $view): void
    {
        ////////auth 
        if (auth()->check()) {
            $view->with('sidebarStats', $this->taskService->dashboardStats(auth()->user()));
        }
    }
}
