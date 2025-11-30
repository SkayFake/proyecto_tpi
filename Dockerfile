# Imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilitar mod_rewrite para .htaccess
RUN a2enmod rewrite

# HABILITAR AllowOverride All para permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Instalar extensiones necesarias para MySQL/MariaDB
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Configurar Apache para escuchar en el puerto 8080 (Render lo exige)
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf
RUN sed -i 's/*:80/*:8080/g' /etc/apache2/sites-enabled/000-default.conf

# Copiar el código al directorio público de Apache
WORKDIR /var/www/html
COPY . /var/www/html

# Render lee este puerto
ENV PORT=8080
EXPOSE 8080

# Iniciar Apache (Render detecta este comando automáticamente)
CMD ["apache2-foreground"]


