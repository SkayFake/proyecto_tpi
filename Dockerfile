# Imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilitar mod_rewrite para que funcione .htaccess
RUN a2enmod rewrite

# Copiar todo el proyecto al document root de Apache
WORKDIR /var/www/html
COPY . /var/www/html

# Permitir .htaccess en el vhost principal
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# (Render detecta que Apache escucha en el puerto 80, no hace falta EXPOSE)
