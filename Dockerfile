# Imagen PHP 8.2 con Apache
FROM php:8.2-apache

# Instalamos extensiones para PostgreSQL y habilitamos mod_rewrite
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite

# Configurar Apache para apuntar a la carpeta public (Mejor seguridad y URLs limpias)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiamos todos los archivos del proyecto
COPY . /var/www/html/

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Exponemos el puerto 80
EXPOSE 80
