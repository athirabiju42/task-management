<?php

namespace App\Models;

use App\Enums\AiPriority;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'ai_summary',
        'ai_priority',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'ai_priority' => AiPriority::class,
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status))
            ->when($filters['priority'] ?? null, fn (Builder $q, $priority) => $q->where('priority', $priority))
            ->when($filters['search'] ?? null, function (Builder $q, $search) {
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['assigned_to'] ?? null, fn (Builder $q, $userId) => $q->where('assigned_to', $userId))
            ->when($filters['due_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('due_date', '>=', $date))
            ->when($filters['due_to'] ?? null, fn (Builder $q, $date) => $q->whereDate('due_date', '<=', $date));
    }
}
