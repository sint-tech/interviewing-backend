#!/bin/bash

if [ ! -f vendor/autoload.php ]; then
    composer install --no-progress --no-interaction
fi

if [ ! -f .env ]; then
    echo "Creating .env file"
    cp .env.example .env
else
    echo ".env file already exists"
fi

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    php artisan migrate
    php artisan key:generate
    php artisan optimize:clear
    php artisan optimize
    php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
elif [ "$role" = "queue" ]; then
    echo "Running the queue"
    php /var/www/artisan queue:work --verbose --tries=3 --timeout=90
fi

exec docker-php-entrypoint "$@"