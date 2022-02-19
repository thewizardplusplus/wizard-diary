FROM php:8.0.15-apache-bullseye

RUN apt-get update \
  && apt-get install --assume-yes libpspell-dev aspell-ru \
  && rm --recursive --force /var/lib/apt/lists/* \
  && a2enmod rewrite \
  && docker-php-ext-install pdo_mysql pspell

WORKDIR /var/www/html
COPY --chown=www-data:www-data . ./
COPY --chown=www-data:www-data \
  tools/wait-for-it.sh \
  /usr/local/bin/wait-for-it.sh

USER www-data:www-data
RUN rm --force tools/wait-for-it.sh \
  && mkdir --parents assets protected/runtime \
  && chmod u+x tools/access_code_finder.sh