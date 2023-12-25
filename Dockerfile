FROM ubuntu:20.04

RUN apt-get update && apt-get install -y software-properties-common
RUN LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php

RUN apt-get update -y && apt-get install -y nginx supervisor curl vim
RUN apt-get update -y && apt-get install -y php8.1 \
    php8.1-fpm \
    php8.1-common \
    php8.1-mysql \
    php8.1-gmp \
    php8.1-ldap \
    php8.1-curl \
    php8.1-intl \
    php8.1-mbstring \
    php8.1-xmlrpc \
    php8.1-gd \
    php8.1-bcmath \
    php8.1-xml \
    php8.1-cli \
    php8.1-memcache \
    php8.1-redis \
    php8.1-zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

#Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update

WORKDIR /var/www/html
COPY . .
RUN composer install

COPY ./conf/nginx.conf /etc/nginx/conf.d/default.conf
COPY ./conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80
EXPOSE 443

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
