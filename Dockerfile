FROM composer:2.6.6 as vendor

WORKDIR /tmp/

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist


FROM ubuntu:22.04
RUN apt update
RUN apt install ca-certificates apt-transport-https software-properties-common lsb-release -y
RUN add-apt-repository ppa:ondrej/php -y
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive TZ=Etc/UTC apt-get -y install tzdata
RUN apt-get install -y --allow-change-held-packages \
      php8.2 php8.2-bcmath php8.2-bz2 php8.2-cgi php8.2-cli php8.2-common php8.2-curl php8.2-dba php8.2-dev \
      php8.2-enchant php8.2-fpm php8.2-gd php8.2-gmp php8.2-imap php8.2-interbase php8.2-intl php8.2-ldap \
      php8.2-mbstring php8.2-mysql php8.2-odbc php8.2-opcache php8.2-pgsql php8.2-phpdbg php8.2-pspell php8.2-readline \
      php8.2-xml php8.2-xsl \
      php8.2-zip php8.2-imagick php8.2-memcached php8.2-redis php8.2-xmlrpc

RUN sed -i "s/;ffi.enable =.*/ffi.enable = true/" /etc/php/8.2/cli/php.ini && echo "extension=ffi.so" >> /etc/php/8.2/cli/php.ini

RUN apt-get install -y --no-install-recommends libvips
RUN which php
COPY . /var/www/html
COPY --from=vendor /tmp/vendor/ /var/www/html/vendor/

EXPOSE 8008
CMD ["php", "-S", "0.0.0.0:8008", "-t", "/var/www/html"]
