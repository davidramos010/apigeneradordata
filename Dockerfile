FROM php:8.1-fpm

# Argumentos para usuario no root
ARG user=laravel
ARG uid=1000

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    git \
    curl \
    libonig-dev \
    unzip

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario del sistema
RUN useradd -G www-data,root -u ${uid} -d /home/${user} ${user}
RUN mkdir -p /home/${user}/.composer && \
    chown -R ${user}:${user} /home/${user}

# Establecer el directorio de trabajo
WORKDIR /var/www

# Copiar el contenido de la aplicación
COPY src/ /var/www

# Establecer permisos
RUN chown -R $user:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Cambiar al usuario no root
USER $user

# Exponer el puerto 9000 y iniciar el servidor php-fpm
EXPOSE 9000
CMD ["php-fpm"]