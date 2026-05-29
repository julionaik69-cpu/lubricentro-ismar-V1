FROM php:8.1-apache

# Instalar extensiones necesarias para conectarse a PostgreSQL (Supabase)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Habilitar el módulo de reescritura de Apache (por si usas rutas amigables)
RUN a2enmod rewrite

# Copiar todo el código de tu lubricentro al servidor Apache
COPY . /var/www/html/

# Darle permisos correctos a las carpetas
RUN chown -r www-data:www-data /var/www/html/

EXPOSE 80