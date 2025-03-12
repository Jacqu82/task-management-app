ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

# https://stackoverflow.com/questions/51023312/docker-having-issues-installing-apt-utils
ARG DEBIAN_FRONTEND=noninteractive

# Set timezone
RUN echo "date.timezone = Europe/Warsaw" > /usr/local/etc/php/conf.d/timezone.ini

# for composer
RUN apt-get update \
  && apt-get install -y curl unzip

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions intl pdo_mysql zip gd exif opcache

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

WORKDIR /web/backend

# composer cache directory
RUN mkdir -p /.composer && chown -R 1000 /.composer

CMD ["php-fpm"]

EXPOSE 9000
