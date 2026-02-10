FROM php:8.2-apache

# Install the curl extension (used by fetchTechWeekData)
RUN docker-php-ext-install curl

# Copy application files into Apache document root
COPY index.php /var/www/html/
COPY script.js /var/www/html/
COPY styles.css /var/www/html/
COPY logo.png /var/www/html/
COPY favicon.ico /var/www/html/

EXPOSE 80
