build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

ce:
	cp .env.example .env

ccl:
	php artisan config:clear

rcl:
	php artisan route:clear

app:
	docker-compose exec app bash