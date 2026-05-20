<?php

namespace App\Services;

use App\Enums\AiPriority;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIService
{
    public function generateSummary(Task $task): array
    {
        try {
            if ($this->shouldUseMock()) {
                return $this->mockResponse($task);
            }

            return $this->callProvider($task);
        } catch (\Throwable $e) {
            Log::warning('AI summary generation failed, using mock fallback', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return $this->mockResponse($task);
        }
    }

    protected function shouldUseMock(): bool
    {
        return config('ai.provider') === 'mock'
            || empty(config('ai.openai.api_key'));
    }

    protected function callProvider(Task $task): array
    {
        $prompt = $this->buildPrompt($task);

        $response = Http::withToken(config('ai.openai.api_key'))
            ->timeout(30)
            ->post(config('ai.openai.base_url').'/chat/completions', [
                'model' => config('ai.openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a task management assistant. Respond only with valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('OpenAI API error: '.$response->body());
        }

        $content = $response->json('choices.0.message.content');
        $parsed = json_decode($content, true);

        if (! is_array($parsed) || ! isset($parsed['ai_summary'], $parsed['ai_priority'])) {
            throw new \RuntimeException('Invalid AI response format');
        }

        return [
            'ai_summary' => Str::limit((string) $parsed['ai_summary'], 1000),
            'ai_priority' => AiPriority::tryFrom($parsed['ai_priority'])?->value
                ?? AiPriority::Medium->value,
        ];
    }

    public function buildPrompt(Task $task): string
    {
        $dueDate = $task->due_date?->format('Y-m-d') ?? 'Not set';
        $priority = $task->priority?->value ?? 'unknown';
        $status = $task->status?->value ?? 'unknown';

        return <<<PROMPT
Analyze this task and return JSON with exactly two keys:
- "ai_summary": a concise 2-3 sentence summary of the task and suggested next steps
- "ai_priority": suggested priority as one of: low, medium, high

Task Title: {$task->title}
Description: {$task->description}
Current Priority: {$priority}
Status: {$status}
Due Date: {$dueDate}

Respond with JSON only, no markdown.
PROMPT;
    }

    protected function mockResponse(Task $task): array
    {
        $priority = match (true) {
            str_contains(strtolower($task->title), 'urgent') => AiPriority::High,
            str_contains(strtolower($task->title), 'low') => AiPriority::Low,
            default => AiPriority::Medium,
        };

        $summary = sprintf(
            'Task "%s" is currently %s with %s priority. %s',
            $task->title,
            $task->status?->label() ?? 'pending',
            $task->priority?->label() ?? 'medium',
            $task->due_date
                ? 'Due by '.$task->due_date->format('M j, Y').'.'
                : 'No due date set.'
        );

        return [
            'ai_summary' => $summary,
            'ai_priority' => $priority->value,
        ];
    }
}
