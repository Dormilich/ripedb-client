ARG PHP_VERSION=7.4

FROM php:${PHP_VERSION}-fpm-alpine AS php_cli

ENV TZ Europe/Berlin

RUN set -ex && \
    apk update && \
    apk upgrade && \
    apk add --no-cache tzdata && \
    cp /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    apk del tzdata && \
    rm -rf /tmp/* /var/cache/apk/*;

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

VOLUME /var/run/php

# for PHP <7.2.5 use composer 2.2
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV PATH="${PATH}:/root/.composer/vendor/bin"
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/usr/local/share/composer

WORKDIR /srv/app

COPY . .

CMD ["php-fpm"]
