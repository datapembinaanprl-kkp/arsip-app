FROM php:8.3-cli

WORKDIR /app

RUN apt update && apt install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]