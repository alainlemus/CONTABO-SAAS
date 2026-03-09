# ---------- STAGE 1: Build frontend ----------
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# ---------- STAGE 2: PHP ----------
FROM php:8.2-fpm-alpine

# Dependencias del sistema
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    postgresql-dev \
    zip \
    unzip \
    git

# Configurar GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    gd \
    bcmath \
    zip \
    opcache

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Copiar assets compilados desde node stage
COPY --from=node_builder /app/public/build public/build

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
