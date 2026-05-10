FROM php:8.4-cli-alpine

WORKDIR /app

RUN apk add --no-cache postgresql-client libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN mkdir -p bootstrap/cache storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R 775 bootstrap/cache storage \
    && composer install --no-dev --optimize-autoloader

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]