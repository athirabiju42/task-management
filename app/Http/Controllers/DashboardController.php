<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    public function __invoke(Request $request): View
    {
        $stats = $this->taskService->dashboardStats($request->user());

        return view('dashboard', compact('stats'));
    }
}
