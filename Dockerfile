# importamos la imagen
FROM php:8.2-apache-bullseye

# ====================
# Instalamos dependencias del sistema + las extensiones de PHP
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
# Copiamos el código de nuestra aplicación
# ====================
WORKDIR /var/www/html

# Copiamos todo el proyecto
COPY . /var/www/html

# ====================
# Fase de permisos
# ====================
# 1. Creamos la carpeta
# 2. Asignamos al usuario de apache (www-data) como dueño
# 3. Damos permisos 775 para que el servidor pueda escribir las fotos
RUN mkdir -p /var/www/html/web/img/perfil \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/web/img/perfil

# exponemos el puerto
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]