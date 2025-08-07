FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev gnupg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Node.js v20 LTS dan Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable mod_rewrite for Laravel
RUN a2enmod rewrite

# Copy Apache config
COPY docker/php/apache.conf /etc/apache2/sites-available/000-default.conf

# Init script Laravel
COPY docker/php/init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh
ENTRYPOINT ["/usr/local/bin/init.sh"]
