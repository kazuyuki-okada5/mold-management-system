FROM php:8.3-fpm

# 必要パッケージのインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composerインストール
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Node.js / npmインストール
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html