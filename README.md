# AI-Assisted Task Management System

Production-ready Laravel task management application built for the Senior Laravel Machine Test. It demonstrates clean architecture with the **Repository Pattern**, service layers, role-based authorization, REST APIs, and AI integration.

## Tech Stack

- Laravel 12 (compatible with Laravel 10+ requirements)
- MySQL (SQLite supported for local/testing)
- Laravel Breeze (Blade + Tailwind CSS v4)
- Laravel Sanctum (API authentication)
- OpenAI API with mock fallback
- Chart.js (dashboard analytics)

## Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TaskController.php          # Web CRUD (no Eloquent)
│   │   ├── DashboardController.php
│   │   └── Api/TaskController.php      # REST API
│   ├── Requests/                       # Form validation
│   └── Resources/TaskResource.php      # API transformation
├── Models/Task.php                     # Eloquent + filter scopes
├── Repositories/
│   ├── Contracts/TaskRepositoryInterface.php
│   └── Eloquent/TaskRepository.php     # Data access + caching
├── Services/
│   ├── TaskService.php                 # Business logic + transactions
│   └── AIService.php                   # AI prompt + API + fallback
├── Policies/TaskPolicy.php
├── Enums/                              # Role, priority, status
├── Jobs/ProcessTaskAI.php              # Queued AI (bonus)
└── Providers/RepositoryServiceProvider.php
```

### Request Flow

1. **Controller** → validates via Form Request, authorizes via Policy
2. **TaskService** → business rules, transactions, triggers AI
3. **TaskRepository** → database queries only
4. **AIService** → prompt building, API call, mock fallback

Controllers never call Eloquent directly.

## AI Integration

### Prompt (documented)

The AI prompt is built in `AIService::buildPrompt()`:

```
Analyze this task and return JSON with exactly two keys:
- "ai_summary": a concise 2-3 sentence summary of the task and suggested next steps
- "ai_priority": suggested priority as one of: low, medium, high

Task Title: {title}
Description: {description}
Current Priority: {priority}
Status: {status}
Due Date: {due_date}
```

### Configuration

| Variable | Description |
|----------|-------------|
| `AI_PROVIDER` | `mock` or `openai` |
| `OPENAI_API_KEY` | API key (empty = mock fallback) |
| `OPENAI_MODEL` | Default: `gpt-4o-mini` |
| `AI_QUEUE_PROCESSING` | `true` to queue AI via `ProcessTaskAI` job |

### Behavior

- On task create, `TaskService::store()` runs a DB transaction, creates the task, then calls `AIService::generateSummary()`
- API failures automatically fall back to deterministic mock output
- Regenerate via web UI or `GET /api/tasks/{id}/ai-summary`

## Authentication & Roles

| Role | Access |
|------|--------|
| **Admin** | Full access to all tasks, assign users, delete tasks |
| **User** | View/edit only assigned tasks |

### Demo Accounts (after seeding)

| Email | Password | Role |
|-------|----------|------|
| admin@taskmanager.test | password | Admin |
| user@taskmanager.test | password | User |

## API Endpoints

All endpoints require Sanctum token: `Authorization: Bearer {token}`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | List tasks (filtered by role) |
| POST | `/api/tasks` | Create task (+ AI summary) |
| PATCH | `/api/tasks/{id}/status` | Update status |
| GET | `/api/tasks/{id}/ai-summary` | Get/regenerate AI summary |

### Create API Token

```bash
php artisan tinker
>>> $user = User::where('email', 'admin@taskmanager.test')->first();
>>> $user->createToken('api')->plainTextToken;
```

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure DB in .env (MySQL recommended)
php artisan migrate --seed

npm install && npm run build
php artisan serve
```

Visit `http://localhost:8000` and log in with demo credentials.

## Docker (Bonus)

```bash
docker compose up --build
php artisan migrate --seed
```

## Testing

```bash
php artisan test
```

Feature tests cover API authorization, task CRUD, and policy enforcement.

## Screenshots

Add screenshots to `docs/screenshots/`:

- `task-list.png` — Task list with filters
- `task-form.png` — Create/Edit form
- `task-detail.png` — Task detail + AI summary panel
- `dashboard.png` — Analytics dashboard

## Submission Checklist

- [x] Repository layer (`TaskRepositoryInterface` + `TaskRepository`)
- [x] Service layer (`TaskService`, `AIService`)
- [x] No Eloquent in controllers
- [x] Policies & gates
- [x] Form Requests & API Resources
- [x] REST API with proper status codes
- [x] Breeze auth + Admin/User roles
- [x] Dashboard analytics + Chart.js
- [x] AI integration with documented prompt
- [x] Queued AI job (bonus)
- [x] Repository caching (bonus)
- [x] Feature tests (bonus)
- [x] Docker (bonus)
- [x] `.env.example`

## License

MIT
