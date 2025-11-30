# Imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilitar mod_rewrite para que funcione .htaccess
RUN a2enmod rewrite

# Copiar proyecto al document root
WORKDIR /var/www/html
COPY . /var/www/html

# Permitir .htaccess en apache
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Render usa la variable PORT automáticamente, solo redirige Apache al puerto
ENV PORT=8080
EXPOSE 8080

