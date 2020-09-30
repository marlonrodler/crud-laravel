long-wait:
	echo "Take a long sleep..."
	sleep 20

short-wait:
	echo "Take a short sleep..."
	sleep 5

dev-seed:
	docker-compose exec app php artisan db:seed

passport:
	docker-compose exec app php artisan passport:install --force

storage-link:
	docker-compose exec app php artisan storage:link

route-list:
	docker-compose exec app php artisan route:list

migrate:
	docker-compose exec app php artisan migrate

rollback:
	docker-compose exec app php artisan migrate:rollback

autoload:
	docker-compose exec app composer dump-autoload -o

app-key:
	docker-compose exec app php artisan key:generate

install:
	composer install

up:
	docker-compose up -d

build:
	docker-compose build

dev: up install app-key storage-link long-wait migrate short-wait passport dev-seed

down:
	docker-compose down

down-v:
	docker-compose down -v

pull-master:
	git pull origin master

prod-build:
	docker-compose -f docker-compose.prod.yml down -v \
	&& docker-compose -f docker-compose.prod.yml build \
	&& docker-compose -f docker-compose.prod.yml up -d \
    && docker-compose -f docker-compose.prod.yml exec app php artisan storage:link

cache-prod:
	docker-compose -f docker-compose.prod.yml exec app php artisan config:cache \
	&& docker-compose -f docker-compose.prod.yml exec app php artisan route:cache

prod: pull-master prod-build cache-prod

pull-homolog:
	git pull origin homolog

homolog-build:
	docker-compose -f docker-compose.hom.yml down -v \
	&& docker-compose -f docker-compose.hom.yml build \
	&& docker-compose -f docker-compose.hom.yml up -d \
    && docker-compose -f docker-compose.hom.yml exec app php artisan storage:link

cache-homolog:
	docker-compose -f docker-compose.hom.yml exec app php artisan config:cache \
	&& docker-compose -f docker-compose.hom.yml exec app php artisan route:cache

homolog: pull-homolog homolog-build cache-homolog
