# ------------------------
# Config vars
# ------------------------
APP_CONTAINER=laravel8_app
DB_NAME=laravel
DB_USER=root
DB_PASS=root

# ------------------------
# Main commands
# ------------------------

# Build and start containers + install deps + keys + migrate
deploy: up composer-install app-key jwt-install jwt-secret migrate

# Build and start containers (detached)
up:
	docker-compose up --build -d

# Stop and remove containers
down:
	docker-compose down

# Restart containers
restart: down up

# ------------------------
# Inside container commands
# ------------------------

# Access app container shell
bash:
	docker exec -it $(APP_CONTAINER) bash

# Install PHP dependencies
composer-install:
	docker exec -it $(APP_CONTAINER) composer install

# Generate Laravel app key
app-key:
	docker exec -it $(APP_CONTAINER) php artisan key:generate --force


# Install JWT package and publish vendor
jwt-install:
	docker exec -it $(APP_CONTAINER) composer require tymon/jwt-auth
	docker exec -it $(APP_CONTAINER) php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# Generate JWT secret key
jwt-secret:
	docker exec -it $(APP_CONTAINER) php artisan jwt:secret --force


# Run database migrations
migrate:
	docker exec -it $(APP_CONTAINER) php artisan migrate --force

# Drop all tables and re-run all migrations (fresh start)
migrate-fresh:
	docker exec -it $(APP_CONTAINER) php artisan migrate:fresh --force

# Run database seeders
seed:
	docker exec -it $(APP_CONTAINER) php artisan db:seed

# Clear Laravel caches
cache-clear:
	docker exec -it $(APP_CONTAINER) php artisan cache:clear
	docker exec -it $(APP_CONTAINER) php artisan config:clear
	docker exec -it $(APP_CONTAINER) php artisan route:clear
	docker exec -it $(APP_CONTAINER) php artisan view:clear