#syntax=docker/dockerfile:1.4

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target

# Builder images
FROM composer/composer:2-bin AS composer

FROM mlocati/php-extension-installer:latest AS php_extension_installer

# Build Caddy
FROM caddy:2.6-builder-alpine AS symfony_caddy_builder

RUN xcaddy build

# "php" stage
FROM php:8.2-fpm-alpine AS symfony_php

ENV APP_ENV=prod

WORKDIR /srv/app

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=php_extension_installer --link /usr/bin/install-php-extensions /usr/local/bin/

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
	;

RUN set -eux; \
  install-php-extensions \
    intl \
    zip \
    pdo_mysql \
    apcu \
    opcache \
  ;

###> recipes ###
###< recipes ###

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

COPY --link docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

COPY --link docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY --link docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM symfony_php AS symfony_php_build

RUN apk add --no-cache \
    git \
    yarn \
    ;

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer --link /composer /usr/bin/composer

COPY --link composer.* symfony.* package.* yarn.* ./
RUN set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
    composer clear-cache; \
    yarn install

COPY --link *.js *.json ./
#COPY --link assets assets/
#RUN set -eux; \
#  yarn build

# copy sources
COPY --link  . ./
RUN rm -Rf docker/

RUN set -eux; \
  mkdir -p var/cache var/log; \
  composer dump-autoload --classmap-authoritative --no-dev; \
  composer dump-env prod; \
  chmod +x bin/console; sync;

FROM symfony_php AS symfony_php_serve

COPY --from=symfony_php_build --link /srv/app/bin bin/
COPY --from=symfony_php_build --link /srv/app/config config/
COPY --from=symfony_php_build --link /srv/app/migrations migrations/
COPY --from=symfony_php_build --link /srv/app/public public/
COPY --from=symfony_php_build --link /srv/app/src src/
COPY --from=symfony_php_build --link /srv/app/templates templates/
COPY --from=symfony_php_build --link /srv/app/translations translations/
COPY --from=symfony_php_build --link /srv/app/vendor vendor/
COPY --from=symfony_php_build --link /srv/app/.env.local.php .env.local.php
COPY --from=symfony_php_build --link /srv/app/composer.json composer.json
COPY --from=symfony_php_build --link /srv/app/composer.lock composer.lock

FROM symfony_php_build AS symfony_php_debug

ENV APP_ENV=dev XDEBUG_MODE=off
VOLUME /srv/app/var/

RUN rm "$PHP_INI_DIR/conf.d/app.prod.ini"; \
	mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
	mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

RUN set -eux; \
	install-php-extensions xdebug

RUN rm -f .env.local.php

# Caddy image
FROM caddy:2.6-alpine AS symfony_caddy

WORKDIR /srv/app

COPY --from=symfony_caddy_builder --link /usr/bin/caddy /usr/bin/caddy
COPY --from=symfony_php_build --link /srv/app/public public/
COPY --link docker/caddy/Caddyfile /etc/caddy/Caddyfile
