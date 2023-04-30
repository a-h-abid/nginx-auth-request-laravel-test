FROM php:8.2.5-apache

ENV COMPOSER_VERSION=2.5.5

RUN echo "-- Install/Upgrade APT Dependencies --" \
        && apt update \
        && apt upgrade -y \
        && apt install -V -y --no-install-recommends --no-install-suggests \
            bc \
            curl \
            zip \
            unzip \
    && echo "-- Install PHP Extensions --" \
        && curl -L -o /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
        && chmod a+x /usr/local/bin/install-php-extensions \
        && sync \
        && install-php-extensions \
            exif \
            intl \
            opcache \
            pcntl \
            sockets \
            zip \
    && echo "--- Setting Up Apache ---" \
        && a2enmod rewrite headers \
    && echo "--- Clean Up ---" \
        && apt clean -y \
        && apt autoremove -y

RUN curl -L -o /usr/local/bin/composer https://github.com/composer/composer/releases/download/${COMPOSER_VERSION}/composer.phar \
    && mkdir /run/php \
    && chmod ugo+x /usr/local/bin/composer \
    && composer --version

ARG UID="1000"
ARG GID="1000"

RUN groupadd --gid ${GID} app \
    && useradd --uid ${UID} --create-home  --system --comment "App User" --shell /bin/bash --gid app app

USER app

RUN mkdir /home/app/html

WORKDIR /home/app/html

COPY ./docker-app/000-default.conf /etc/apache2/sites-available/
