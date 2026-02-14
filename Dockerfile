# importamos la imagen
FROM php:8.2-apache-bullseye

# ====================
# Instalamos dependencias del sistema + extensiones PHP
# ====================
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        zip \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ====================
# Copiamos el código de la aplicación
# ====================
WORKDIR /var/www/html

# Copiamos todo el proyecto
COPY . /var/www/html

# ====================
# damos permisos
# ====================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# exponemos el puerto
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]