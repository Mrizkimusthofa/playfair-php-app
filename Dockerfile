# Gunakan image PHP dengan Apache bawaan
FROM php:8.2-apache

# Copy seluruh file project ke dalam folder server Apache
COPY . /var/www/html/

# Set folder public sebagai document root
WORKDIR /var/www/html/public

# Expose port 80
EXPOSE 80

# Jalankan Apache
CMD ["apache2-foreground"]
