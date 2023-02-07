FROM php:7.4.30-fpm as base

ENV LC_ALL C.UTF-8
ENV LANG C.UTF-8

ENV EXT_IMAGICK_VERSION 3.4.4
ENV EXT_APCU_VERSION 5.1.18
ENV EXT_SWOOLE_VERSION 4.8.7
ENV EXT_MONGODB_VERSION 1.6.1
ENV EXT_REDIS_VERSION 5.1.1

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
      && rm -rf /var/lib/apt/lists/* \
      && docker-php-ext-install -j$(nproc) \
      intl \
      bcmath \
      pdo_mysql \
      sockets \
      zip \
      calendar \
      gettext \
      && docker-php-ext-configure gd --with-freetype --with-jpeg \
      && docker-php-ext-install -j$(nproc) gd

RUN pecl install imagick-${EXT_IMAGICK_VERSION}  \
      && pecl install apcu-${EXT_APCU_VERSION} \
      && pecl install swoole-${EXT_SWOOLE_VERSION} \
      && pecl install mongodb-${EXT_MONGODB_VERSION} \
      && pecl install redis-${EXT_REDIS_VERSION} \
      && docker-php-ext-enable imagick apcu swoole mongodb redis \
      && rm -rf /tmp/pear

WORKDIR /srv/www

FROM base as prod

FROM base as dev

ENV EXT_XDEBUG_VERSION 2.8.1

RUN apt-get update && apt-get install -y git unzip vim htop \
      && rm -rf /var/lib/apt/lists/* \
      && pecl install xdebug-${EXT_XDEBUG_VERSION} \
      && docker-php-ext-enable xdebug  \
      && rm -rf /tmp/pear

RUN mkdir /root/.ssh/ && \
      touch /root/.ssh/id_rsa && \
      chmod 0700 /root/.ssh && \
      chmod 0600 /root/.ssh/id_rsa && \
      touch /root/.ssh/known_hosts && \
      ssh-keyscan git.seiue.com >> /root/.ssh/known_hosts && \
      ssh-keyscan github.com >> /root/.ssh/known_hosts

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer
RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/