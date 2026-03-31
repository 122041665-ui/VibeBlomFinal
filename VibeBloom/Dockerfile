FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nodejs \
    npm \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN mkdir -p bootstrap/cache storage/logs storage/framework/cache \
    storage/framework/sessions storage/framework/views \
    && chmod -R 775 bootstrap/cache storage

RUN composer install --no-dev --optimize-autoloader

RUN npm install && APP_URL=https://vibebloom-production.up.railway.app npm run build

EXPOSE 8080

CMD php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=$PORT