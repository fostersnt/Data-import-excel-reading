#!/bin/bash

if [! -f '../vender/autoload.php']; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

if [ ! -f "../.env" ]; then
    echo "Creating .env file"
    cp ../.env.example ../.env
else
    echo ".env file is present"
fi

a2ensite /etc/apache2/sites-available/dataimport.conf
# systemctl restart apache2

# Running artisan commands
php artisan key:generate
php artisan migrate
php artisan optimize:clear
php artisan optimize

# MY_PORT is declared in the Dockerfile
php artisan serve --port=${MY_PORT} --host=0.0.0.0 --env=.env

exec docker-php-entrypoint "$@"
