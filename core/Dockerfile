FROM php:8.3-cli-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* ./

RUN composer install --no-dev --no-scripts --prefer-dist --no-progress && composer clear-cache

COPY . .

ENTRYPOINT ["php", "bin/console.php"]
