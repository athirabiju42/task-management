<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TaskLayout extends Component
{
    public function __construct(
        public string $title = 'Tasks',
        public bool $showFilters = true,
    ) {}

    public function render(): View
    {
        return view('layouts.task');
    }
}
