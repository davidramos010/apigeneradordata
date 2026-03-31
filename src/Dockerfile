# Dockerfile para Laravel (PHP 8.2, Composer, extensiones comunes)
FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update \
    && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        git \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Instala Xdebug para depuración remota (PHPStorm, VS Code, etc.)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configuración básica de Xdebug
RUN { \
      echo "xdebug.mode=develop,debug"; \
      echo "xdebug.start_with_request=yes"; \
      echo "xdebug.client_host=host.docker.internal"; \
      echo "xdebug.client_port=9003"; \
      echo "xdebug.log=/tmp/xdebug.log"; \
    } > /usr/local/etc/php/conf.d/xdebug.ini

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Define permisos iniciales de directorio
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000
CMD ["/usr/local/bin/docker-entrypoint.sh"]
