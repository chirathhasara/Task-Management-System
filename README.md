# Task Manager Application

A modern task management application built with Laravel and Sanctum authentication. Users can create, manage, filter, and organize tasks with a clean MVC architecture.

## Features

- **User Authentication**
  - Register and login with email/password
  - Token-based authentication using Laravel Sanctum
  - Secure profile management with password updates
  - Single device logout

- **Task Management**
  - Create, read, update, and delete tasks
  - Mark tasks as completed or pending
  - Filter tasks by status (all, pending, completed)
  - Soft delete with recycle bin functionality
  - Restore or permanently delete tasks
  - Task ownership and authorization policies

- **User Interface**
  - Professional, responsive design
  - Clean interface without emojis
  - AJAX-based operations for smooth UX
  - Real-time updates without page refresh
  - Pagination support (10 items per page)

- **Architecture**
  - Clean MVC architecture
  - Service layer for business logic
  - Request validation layer
  - Model layer for database queries
  - Policy-based authorization

## Tech Stack

- **Backend:** Laravel 12.x
- **Authentication:** Laravel Sanctum
- **Database:** MySQL
- **Frontend:** Blade Templates, Vanilla JavaScript
- **Build Tool:** Vite
- **Testing:** Pest PHP
- **PHP Version:** 8.2+

## Prerequisites

Before you begin, ensure you have the following installed:

- PHP >= 8.2
- Composer
- Node.js >= 18.x and npm
- MySQL >= 8.0
- Git

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd task-manager
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Update the `.env` file with your configuration (see [Environment Variables](#environment-variables) section below).

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Configure Database

Create a MySQL database for the application:

```sql
CREATE DATABASE task_manager;
```

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Build Frontend Assets

For production:
```bash
npm run build
```

For development (with hot reload):
```bash
npm run dev
```

### 9. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### 10. (Optional) Seed Test Data

To populate the database with test data for testing pagination and features:

```bash
php artisan db:seed --class=TaskSeeder
```

This will create:
- 1 test user (email: `test@example.com`, password: `password123`)
- 15 pending tasks
- 12 completed tasks
- 8 deleted tasks (in recycle bin)

## Environment Variables

### Application Settings

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `APP_NAME` | Application name | Laravel | Yes |
| `APP_ENV` | Environment (local, production) | local | Yes |
| `APP_KEY` | Encryption key (auto-generated) | - | Yes |
| `APP_DEBUG` | Enable debug mode | true | Yes |
| `APP_URL` | Application URL | http://localhost | Yes |

### Database Configuration

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `DB_CONNECTION` | Database driver | mysql | Yes |
| `DB_HOST` | Database host | 127.0.0.1 | Yes |
| `DB_PORT` | Database port | 3306 | Yes |
| `DB_DATABASE` | Database name | task_manager | Yes |
| `DB_USERNAME` | Database username | root | Yes |
| `DB_PASSWORD` | Database password | - | Yes |

### Session Configuration

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `SESSION_DRIVER` | Session storage driver | database | Yes |
| `SESSION_LIFETIME` | Session lifetime in minutes | 120 | Yes |

### Cache & Queue

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `CACHE_STORE` | Cache driver | database | Yes |
| `QUEUE_CONNECTION` | Queue driver | database | Yes |

### Logging

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `LOG_CHANNEL` | Logging channel | stack | Yes |
| `LOG_LEVEL` | Minimum log level | debug | Yes |

### Mail Configuration (Optional)

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `MAIL_MAILER` | Mail driver | log | No |
| `MAIL_HOST` | Mail server host | 127.0.0.1 | No |
| `MAIL_PORT` | Mail server port | 2525 | No |
| `MAIL_USERNAME` | Mail username | null | No |
| `MAIL_PASSWORD` | Mail password | null | No |
| `MAIL_FROM_ADDRESS` | From email address | hello@example.com | No |

### Vite

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `VITE_APP_NAME` | App name for Vite | ${APP_NAME} | Yes |

## Project Structure

```
task-manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Authentication endpoints
│   │   │   └── TaskController.php      # Task CRUD endpoints
│   │   └── Requests/
│   │       ├── LoginRequest.php        # Login validation
│   │       ├── RegisterRequest.php     # Registration validation
│   │       ├── StoreTaskRequest.php    # Task creation validation
│   │       ├── UpdateTaskRequest.php   # Task update validation
│   │       └── UpdateProfileRequest.php # Profile update validation
│   ├── Models/
│   │   ├── Task.php                    # Task model with queries
│   │   └── User.php                    # User model with queries
│   ├── Policies/
│   │   └── TaskPolicy.php              # Task authorization policies
│   └── Services/
│       ├── AuthService.php             # Authentication business logic
│       └── TaskService.php             # Task business logic
├── database/
│   ├── factories/
│   │   ├── TaskFactory.php             # Task factory for testing
│   │   └── UserFactory.php             # User factory for testing
│   ├── migrations/                     # Database migrations
│   └── seeders/
│       └── TaskSeeder.php              # Test data seeder
├── resources/
│   ├── css/
│   │   └── index.css                   # Application styles
│   ├── js/
│   │   └── auth.js                     # Authentication JS helpers
│   └── views/
│       ├── auth/                       # Authentication views
│       ├── tasks/                      # Task management views
│       └── layouts/                    # Layout templates
├── routes/
│   ├── api.php                         # API routes
│   └── web.php                         # Web routes
└── public/                             # Public assets
```

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/register` | Register new user | No |
| POST | `/api/auth/login` | Login user | No |
| POST | `/api/auth/logout-current` | Logout current device | Yes |
| GET | `/api/auth/profile` | Get user profile | Yes |
| PUT | `/api/auth/profile` | Update profile | Yes |

### Tasks

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/tasks` | Get all tasks (supports `?status=pending/completed` and `?page=1`) | Yes |
| POST | `/api/tasks` | Create new task | Yes |
| GET | `/api/tasks/{id}` | Get specific task | Yes |
| PUT | `/api/tasks/{id}` | Update task | Yes |
| DELETE | `/api/tasks/{id}` | Soft delete task | Yes |
| PATCH | `/api/tasks/{id}/complete` | Mark task as completed | Yes |
| PATCH | `/api/tasks/{id}/pending` | Mark task as pending | Yes |
| GET | `/api/tasks/trashed` | Get deleted tasks | Yes |
| POST | `/api/tasks/{id}/restore` | Restore deleted task | Yes |
| DELETE | `/api/tasks/{id}/force` | Permanently delete task | Yes |

## Usage

### 1. Register a New Account

Navigate to `http://localhost:8000/register` and create a new account.

### 2. Login

Login at `http://localhost:8000/login` with your credentials.

### 3. Manage Tasks

- **Create Task:** Navigate to Tasks > Create Task
- **View Tasks:** See all tasks on the Tasks page
- **Filter Tasks:** Use the filter buttons (All, Pending, Completed)
- **Edit Task:** Click the Edit button on any task
- **Delete Task:** Click the Delete button to soft delete
- **View Details:** Click on a task to see full details
- **Toggle Status:** Change task status between pending and completed

### 4. Recycle Bin

- Access deleted tasks from the Recycle Bin link
- Restore tasks back to your task list
- Permanently delete tasks (this cannot be undone)

### 5. Profile Management

- Update your name and email
- Change your password (requires current password)
- Logout from current device

## Testing with Seeded Data

To test pagination and features with sample data:

```bash
# Run the seeder
php artisan db:seed --class=TaskSeeder

# Login with test credentials
Email: test@example.com
Password: password123
```

To reset and start fresh:

```bash
php artisan migrate:fresh --seed --seeder=TaskSeeder
```

## Security Features

- **Token-based Authentication:** Secure API access using Laravel Sanctum
- **Password Hashing:** Bcrypt encryption for passwords
- **Authorization Policies:** Task ownership verification
- **CSRF Protection:** Enabled for web routes
- **Input Validation:** Request validation for all forms
- **SQL Injection Prevention:** Eloquent ORM with parameter binding
- **XSS Protection:** Blade template escaping

## Development

### Running in Development Mode

Start the development server with hot reload:

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev
```

### Code Style

This project follows Laravel coding standards. Run Pint for code formatting:

```bash
./vendor/bin/pint
```

### Testing

Run tests using Pest:

```bash
php artisan test
```

## Troubleshooting

### Database Connection Issues

If you encounter database connection errors:

1. Verify MySQL is running
2. Check database credentials in `.env`
3. Ensure the database exists: `CREATE DATABASE task_manager;`
4. Test connection: `php artisan migrate:status`

### Asset Build Issues

If frontend assets don't load:

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild assets
npm run build
```

### Blade Template Syntax Errors

Avoid using JavaScript template literals (`${variable}`) in Blade files as they conflict with Blade's syntax. Use string concatenation instead:

```javascript
// Bad (causes Blade errors)
const html = `<div>${value}</div>`;

// Good (Blade-safe)
const html = '<div>' + value + '</div>';
```

### Permission Issues

If you encounter permission errors:

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (run as Administrator)
icacls storage /grant Users:F /T
icacls bootstrap/cache /grant Users:F /T
```

## Production Deployment

### Optimization

Before deploying to production:

```bash
# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

### Environment Settings

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues, questions, or contributions, please open an issue in the repository.

---

**Built with ❤️ using Laravel**
