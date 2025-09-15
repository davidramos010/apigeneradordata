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

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos del proyecto (opcional, ya que docker-compose monta el volumen)
# COPY . .

# Da permisos a la carpeta de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000
CMD ["php-fpm"]
