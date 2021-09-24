start:
	php artisan serve --host 127.0.0.1

setup:
	php -r "file_exists('.env') || copy('.env.example', '.env');"
	composer install
	chmod -R 777 storage bootstrap/cache
	php artisan key:generate
	mkdir -p database
	touch database/database.sqlite

watch:
	npm run watch

migrate:
	php artisan migrate

console:
	php artisan tinker

log:
	tail -f storage/logs/laravel.log

test:
	composer exec --verbose phpunit tests
	
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

deploy:
	git push heroku

lint:
	composer run-script phpcs -- --standard=PSR12 routes tests app

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 routes
