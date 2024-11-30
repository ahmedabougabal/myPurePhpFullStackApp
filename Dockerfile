# Use a base PHP image (this is my os of the container -- remember)
FROM php:8.3-apache

# these are the dependencies that are to be Installed (to solve docker related pdo erros (can't load files))
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip

# Copy application code in the current directory
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# this run command to Install Composer (i was facing errors when i tried to run this on my terminal as RUN isnot a ZSH command)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose the necessary port (80 for HTTP - revise the protocols nums of each protocol on the OSI model for refernce (my reminder) )
EXPOSE 80

# Sets the command to start the application
CMD ["apache2-foreground"]