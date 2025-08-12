<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400">
  </a>
</p>

# laravel8-jwt-weather

> Laravel 8 project with JWT authentication and an endpoint to get the weather related to a task.

---

## 🚀 Requirements

- Docker & Docker Compose installed
- Make
- PHP >= 7.4 (for local dev outside containers)
- Composer (for local dev outside containers)
- MySQL or compatible database (can be in container)

---

## 🔧 Installation & Deployment with Docker + Makefile

This project uses Docker containers managed via a `Makefile` to simplify setup and management.

### Main variables (inside Makefile)

| Variable       | Description               |
| -------------- | ------------------------- |
| `APP_CONTAINER`| Laravel app container name |
| `DB_NAME`      | Database name              |
| `DB_USER`      | Database username          |
| `DB_PASS`      | Database password          |

### Common commands

Run these commands from the project root:

| Command          | Description                                  |
|------------------|----------------------------------------------|
| `make deploy`    | Build containers, install deps, generate keys, migrate DB |
| `make up`        | Build and start containers in detached mode  |
| `make down`      | Stop and remove containers                    |
| `make restart`   | Restart containers                            |

### Inside container commands

Use these commands to execute tasks inside the Laravel app container:

| Command              | Description                         |
|----------------------|-----------------------------------|
| `make bash`          | Access app container shell         |
| `make composer-install` | Install PHP dependencies         |
| `make app-key`       | Generate Laravel application key  |
| `make jwt-install`   | Install JWT package & publish vendor files |
| `make jwt-secret`    | Generate JWT secret key            |
| `make migrate`       | Run database migrations            |
| `make migrate-fresh` | Drop all tables and migrate fresh |
| `make seed`          | Run database seeders               |
| `make cache-clear`   | Clear Laravel caches (route, config, view, cache) |

---

## 🏃‍♂️ Running locally (without Docker)

If you prefer running Laravel directly (without Docker), follow these steps:

1. Install PHP dependencies:

```bash
composer install
```

2. Configure `.env` file (copy `.env.example` and fill database and keys).

3. Generate app key:

```bash
php artisan key:generate
```

4. Run migrations and seeders:

```bash
php artisan migrate --seed
```

5. Generate JWT secret key:

```bash
php artisan jwt:secret
```

6. Start the server:

```bash
php artisan serve
```

---

## ⚙️ Clearing cache

Clear Laravel cache and config (both in container or local):

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Or inside container with:

```bash
make cache-clear
```

---

## 📋 Main Endpoints

| Method | Route                    | Description                      | Authentication |
|--------|--------------------------|--------------------------------|----------------|
| POST   | `/api/register`          | User registration               | No             |
| POST   | `/api/login`             | Login and get JWT token         | No             |
| POST   | `/api/logout`            | Logout                         | Yes (JWT)      |
| GET    | `/api/profile`           | Get authenticated user info     | Yes (JWT)      |
| GET    | `/api/tasks`             | List tasks                     | Yes (JWT)      |
| POST   | `/api/tasks`             | Create task                   | Yes (JWT)      |
| GET    | `/api/tasks/{id}`        | Get task detail                | Yes (JWT)      |
| PUT    | `/api/tasks/{id}`        | Update task                   | Yes (JWT)      |
| DELETE | `/api/tasks/{id}`        | Delete task                   | Yes (JWT)      |
| GET    | `/api/tasks/{id}/weather`| Get weather for a task        | Yes (JWT)      |

---

## 🌤️ Using the `/api/tasks/{id}/weather` endpoint

To get the weather related to a task, run:

```bash
curl -X GET http://localhost:8000/api/tasks/1/weather \
-H "Authorization: Bearer <your_jwt_token>"
```

> **Important:** The task must have a valid `due_date` field for this endpoint to work correctly.

---

If you need further assistance, feel free to ask. Happy coding! 🚀