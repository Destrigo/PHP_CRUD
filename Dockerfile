# Base PHP image with Apache
FROM php:8.2-apache

# Install unzip, curl, and PDO MySQL extension
RUN apt-get update && apt-get install -y unzip curl \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP source code
COPY src/ /var/www/html/

# Expose port 80
EXPOSE 80

