# docker image base
ARG BASE_NGINX=nginx:1.21.5-alpine
ARG BASE_PHP=php:8.2-fpm-alpine

FROM $BASE_NGINX as nginx-builder
COPY .docker/nginx/templates/* /etc/nginx/templates/
## php-fpm-alpine-builder image: install common php extension need for project
FROM $BASE_PHP AS php-fpm-alpine-builder
LABEL maintainer="huypq <phanquochuyqthm@gmail.com>"
ENV TZ=Asia/Tokyo
RUN set -eux; \
        \
        apk update; \
        \
        echo "CHANGE TIME ZONE TO '$TZ' AT BUILD TIME ONLY"; \
            apk add --no-cache tzdata; \
            cp /usr/share/zoneinfo/$TZ /etc/localtime; \
            echo $TZ > /etc/timezone; \
            echo "Test current date: $(date)"; \
            echo "timezone will reset to UTC if we run command 'apk del tzdata' so no run command until this bug fixed by alpine maintainer"; \
        \
        echo "INSTALL  PHP EXTENSION NEEDED 'pdo_mysql' 'opcache'"; \
            apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
            docker-php-ext-install -j "$(nproc)" pdo_mysql; \
        \
        echo 'CLEAN UP TEMP TO REDUCE DOCKER IMAGE SIZES'; \
        rm -rf /tmp/* /var/cache/apk/*

#####################################################################################################################
## charts-public: bundle  public source code (js,css). use this image on read-only env (production, staging) ##
#####################################################################################################################
FROM nginx-builder as charts-public

# tag help inspect docker image in the future
ARG GIT_COMMIT_HASH=unspecified
ARG GIT_BRANCH_NAME=unspecified

LABEL maintainer="huypq <phanquochuyqthm@gmail.com>" \
    git.commit=$GIT_COMMIT_HASH \
    git.branch=$GIT_BRANCH_NAME

WORKDIR /var/www/html/charts/public
COPY .docker/nginx/conf.d/charts-app.conf /etc/nginx/conf.d/default.conf
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --chown=nginx:nginx public .
EXPOSE 80

##########################################################################################################
## charts-vendor ##
##########################################################################################################
FROM composer:2.3.5 AS vendor
WORKDIR /var/www/html/charts
COPY --chown=www-data:www-data composer.json artisan ./
RUN set -eux; \
composer install \
        --quiet \
        --ignore-platform-reqs \
        --no-interaction \
        --no-progress \
        --no-suggest \
        --no-scripts \
        --prefer-dist \
        --no-autoloader \
        --no-dev;

## php-fpm-dev image: install php extension support for developer
FROM php-fpm-alpine-builder AS php-fpm-dev
ARG USER_ID=1000
ARG GROUP_ID=1000
ENV COMPOSER_CACHE_DIR=/tmp
WORKDIR /var/www/html/charts

RUN set -eux; \
        echo "INSTALL NPM nodejs, Xdebug"; \
            apk add nodejs-current;\
            apk add --update npm;\
            apk add --no-cache linux-headers;\
            apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
            pecl install xdebug-3.2.2; \
            docker-php-ext-enable xdebug; \
        \
        apk del .build-deps; \
        cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; \
        \
        echo 'CLEAN UP TEMP TO REDUCE DOCKER IMAGE SIZES'; \
        rm -rf /tmp/* /var/cache/apk/*
COPY .docker/php/conf.d/z-xdebug.dev.ini .docker/php/conf.d/z-php.dev.ini $PHP_INI_DIR/conf.d/
COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer
