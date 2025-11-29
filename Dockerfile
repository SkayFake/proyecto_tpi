FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar proyecto al servidor
COPY . /var/www/html/

# Dar permisos
RUN chmod -R 755 /var/www/html

# Exponer el puerto web
EXPOSE 80