FROM php:8.2-apache

# Activar mod_rewrite
RUN a2enmod rewrite

# Permitir que .htaccess funcione
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar proyecto al servidor
COPY . /var/www/html/

# Dar permisos
RUN chmod -R 755 /var/www/html

# Exponer el puerto web
EXPOSE 80


