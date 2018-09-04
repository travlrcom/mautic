FROM php:7.1-apache

# Install PHP extensions
RUN apt-get update \
  && apt-get install \
    --no-install-recommends \
    --yes \
    --allow-unauthenticated \
      cron \
      git \
      wget \
      libc-client-dev \
      libicu-dev \
      libkrb5-dev \
      libmcrypt-dev \
      libgd-dev \
      libssl-dev \
      zlib1g-dev \
      unzip \
      zip \
      supervisor \
  && apt-get purge --yes --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
  && rm -rf /var/lib/apt/lists/* \
  && rm /etc/cron.daily/* \
  && docker-php-ext-configure imap --with-imap --with-imap-ssl --with-kerberos \
  && docker-php-ext-install imap intl mcrypt mysqli pdo_mysql zip bcmath \
  && docker-php-ext-configure gd \
    --with-gd \
    --with-freetype-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install gd \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
  && apt-get clean \
  && apt-get autoremove --purge --yes

# Define Mautic version and expected SHA1 signature
ENV MAUTIC_VERSION 2.14.0

# By default enable cron jobs
ENV MAUTIC_RUN_CRON_JOBS true

# Setting an root user for test
ENV MAUTIC_DB_USER mautic_master
ENV MAUTIC_DB_NAME mautic

# Download package and extract to web volume
WORKDIR /var/www/html
COPY . . 

RUN rm -Rf /var/www/html/app/cache/*

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN (composer install --no-dev --optimize-autoloader --prefer-dist)


# Copy init scripts and custom .htaccess
COPY ./docker-manifest/mautic.crontab /etc/cron.d/mautic
COPY ./docker-manifest/mautic-php.ini /usr/local/etc/php/conf.d/mautic-php.ini
COPY ./docker-manifest/local.php /var/www/html/app/config/local.php
COPY ./docker-manifest/supervisor/conf.d/* /etc/supervisor/conf.d/
COPY ./docker-manifest/entrypoint.sh /entrypoint.sh

# Enable Apache Rewrite Module
RUN a2enmod rewrite \
  && cd /var/www/html \
  && php app/console cache:warmup \
  && chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html/media/images \
  && chmod a+x /entrypoint.sh

CMD /entrypoint.sh
