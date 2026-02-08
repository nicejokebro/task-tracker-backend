FROM php:8.4-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    g++ \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        zip \
        intl \
        exif \
        pcntl \
        bcmath \
        opcache \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка Xdebug (совместимый с PHP 8.4)
RUN pecl install xdebug-3.4.0 \
    && docker-php-ext-enable xdebug

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем composer.json и .env
COPY composer.json composer.lock symfony.lock ./
COPY .env .env.local ./

# Устанавливаем зависимости
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Копируем остальные файлы
COPY . .

# Права
RUN chown -R www-data:www-data /var/www/html/var \
    && chmod -R 775 /var/www/html/var

USER www-data

CMD ["php-fpm"]