#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
if [ ! -e .env ]
 then cp .env.example .env
fi

if [ ! -e .env.testing ]
 then cp .env.testing.example .env.testing
fi

chown -R www-data:www-data .
composer install
php artisan key:generate
php artisan migrate

php-fpm
