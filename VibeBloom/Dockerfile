FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo pdo_mysql

RUN printf "upload_max_filesize=8M\npost_max_size=24M\nmax_file_uploads=10\n" > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install

EXPOSE 8000

CMD ["sh", "-c", "if [ -f .env ] && [ -n \"$FASTAPI_URL\" ]; then sed -i \"s#^FASTAPI_URL=.*#FASTAPI_URL=$FASTAPI_URL#\" .env; fi && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan migrate --force && php artisan storage:link || true; php artisan serve --host=0.0.0.0 --port=8000"]
