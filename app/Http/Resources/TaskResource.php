<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority?->value,
            'status' => $this->status?->value,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'assigned_to' => $this->assigned_to,
            'assignee' => $this->whenLoaded('assignee', fn () => [
            'id' => $this->assignee->id,
            'name' => $this->assignee->name,
                'email' => $this->assignee->email,

            ]),
            'ai_summary' => $this->ai_summary,
            'ai_priority' => $this->ai_priority?->value,
            
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
