FROM ubuntu:20.04

# Set working directory
WORKDIR /var/www/app

## use this only when use "docker run" without "bind" version (see scripts/run-dev-env.sh)
#COPY . /var/www/app

# Install system dependencies
RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install curl -y
RUN export DEBIAN_FRONTEND=noninteractive
RUN apt install tzdata
RUN apt install -q curl php-bcmath unzip php-common php-fpm php-json php-mbstring php-zip php-cli php-xml curl php-tokenizer php-mysql php-curl php-gd php-xml php-bcmath php-pear npm -y
RUN apt install mysql-client -y
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
RUN /usr/local/bin/composer global require laravel/installer
RUN /usr/local/bin/composer global require aws/aws-sdk-php

RUN curl -fsSL https://deb.nodesource.com/setup_17.x | bash -
RUN apt-get install -y nodejs

#RUN composer install
#RUN npm install

# CMD composer install; sleep 10; npm install; sleep 10; php artisan migrate; php artisan serve --host 0.0.0.0 --port 3000
CMD composer install; sleep 10; npm install; sleep 10; php artisan migrate; php artisan serve --host 0.0.0.0 --port 3000


