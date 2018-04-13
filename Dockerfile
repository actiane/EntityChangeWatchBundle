FROM php:7.2-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk update \
    && apk add --no-cache git curl sqlite sqlite-dev openssh-client icu libpng libjpeg-turbo \
    && apk add --no-cache --virtual build-dependencies icu-dev \
                                libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev g++ make autoconf libtool \
    && docker-php-ext-install pdo_sqlite intl zip gd exif

ENV XDEBUG_VERSION=2.6.0 \
   PHP_INI_DIR=/usr/local/etc/php

RUN git clone --depth=1 -b ${XDEBUG_VERSION} https://github.com/xdebug/xdebug.git /tmp/php-xdebug && \
        cd /tmp/php-xdebug && \
        sh ./rebuild.sh && cd .. && rm -rf /tmp/php-xdebug/ \
        && echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

RUN apk del build-dependencies

RUN echo "memory_limit=1024M" > /usr/local/etc/php/php.ini

COPY --from=composer:1.6 /usr/bin/composer /usr/bin/composer

RUN mkdir /app
WORKDIR /app

RUN wget https://github.com/php-coveralls/php-coveralls/releases/download/v2.0.0/php-coveralls.phar
RUN chmod +x php-coveralls.phar

ENV PATH /root/.composer/vendor/bin:$PATH
CMD ["/app/vendor/bin/phpunit"]
