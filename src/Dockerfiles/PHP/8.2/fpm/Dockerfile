FROM php:8.2.4-fpm as base

ENV LC_ALL C.UTF-8
ENV LANG C.UTF-8
ENV PHP_CS_FIXER_IGNORE_ENV true

ENV EXT_IMAGICK_VERSION 3.7.0
ENV EXT_APCU_VERSION 5.1.22
ENV EXT_SWOOLE_VERSION 4.8.13
ENV EXT_MONGODB_VERSION 1.15.1
ENV EXT_REDIS_VERSION 5.3.7

RUN sed -i 's/\(security\|deb\).debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list
RUN ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime

RUN apt-get clean && apt-get update && apt-get install -y \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      libcurl4-openssl-dev \
      libpng-dev \
      libicu-dev \
      libmcrypt-dev \
      libreadline-dev \
      libzip-dev \
      pkg-config \
      libssl-dev \
      libmagickwand-dev \
      wget \
      && rm -rf /var/lib/apt/lists/* \
      && docker-php-ext-install -j$(nproc) \
      intl \
      bcmath \
      pdo_mysql \
      sockets \
      zip \
      calendar \
      gettext \
      pcntl \
      sysvsem \
      && docker-php-ext-configure gd --with-freetype --with-jpeg \
      && docker-php-ext-install -j$(nproc) gd

RUN pecl install imagick-${EXT_IMAGICK_VERSION} \
      && pecl install apcu-${EXT_APCU_VERSION} \
      && pecl install swoole-${EXT_SWOOLE_VERSION} \
      && pecl install mongodb-${EXT_MONGODB_VERSION} \
      && pecl install redis-${EXT_REDIS_VERSION} \
      && docker-php-ext-enable imagick apcu swoole mongodb redis \
      && rm -rf /tmp/pear

WORKDIR /srv/www

FROM base as prod

FROM base as dev

ENV EXT_XDEBUG_VERSION 3.2.1

RUN apt-get update && apt-get install -y git unzip vim htop exuberant-ctags \
      && rm -rf /var/lib/apt/lists/* \
      && pecl install xdebug-${EXT_XDEBUG_VERSION} pcov \
      && docker-php-ext-enable xdebug pcov \
      && rm -rf /tmp/pear

RUN mkdir /root/.ssh/ && \
      touch /root/.ssh/id_rsa && \
      chmod 0700 /root/.ssh && \
      chmod 0600 /root/.ssh/id_rsa && \
      touch /root/.ssh/known_hosts && \
      ssh-keyscan git.seiue.com >> /root/.ssh/known_hosts && \
      ssh-keyscan github.com >> /root/.ssh/known_hosts

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer
RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/