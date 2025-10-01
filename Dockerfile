FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libfreetype6-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    ffmpeg \
    libonig-dev \
    libxml2-dev \
    libssh2-1-dev \
    libssh2-1 \
    zip \
    unzip \
    npm \
    strace \
    lsof

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath -j$(nproc) gd opcache
RUN pecl install ssh2-1.4 xdebug
RUN docker-php-ext-enable ssh2 gd xdebug

# Configure PHP
COPY ./docker/php/conf.d/* /usr/local/etc/php/conf.d/

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy helper script and make it executeable
COPY ./docker/scripts/prepare-docker-setup.sh /tmp/
RUN chmod +x /tmp/prepare-docker-setup.sh

# Grant www-data permissions on Laravel installation
RUN chown -R www-data:www-data /var/www/

# Set working directory
WORKDIR /var/www

USER www-data
