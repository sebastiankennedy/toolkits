FROM centos:centos7.4.1708

# step1 - install php
RUN yum -y install epel-release \
    && yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
    && yum -y install https://rpms.remirepo.net/enterprise/remi-release-7.rpm

RUN yum -y install yum-utils  \
    && yum-config-manager --enable remi-php74

RUN yum -y update

RUN yum -y install php \
    php-cli \
    php-fpm \
    php-mysqlnd \
    php-zip \
    php-devel \
    php-gd \
    php-mcrypt \
    php-mbstring \
    php-curl \
    php-xml \
    php-pear \
    php-bcmath \
    php-json

RUN php -v && php --modules

# step2 - install composer
RUN yum -y install curl
RUN curl -sS "https://getcomposer.org/installer" | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod -R 777 /usr/local/bin/composer \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# step3 - install pdf package
RUN yum -y install fontconfig \
    libXrender \
    libXext \
    xorg-x11-fonts-Type1 \
    xorg-x11-fonts-75dpi \
    freetype \
    libpng \
    zlib \
    libjpeg-turbo

# step 4 - install chinese fonts
RUN yum -y install fontconfig \
    && mkdir -p /usr/share/fonts/chinese \
    && chmod -R 755 /usr/share/fonts/chinese \
    && yum -y groupinstall Fonts \
    && cd /usr/share/fonts/chinese \
    && fc-list :lang=zh

RUN mkdir -p /root/.ssh \
    && touch /root/.ssh/id_rsa

# step 5 - mkdir workspace
RUN mkdir -p /srv/www
WORKDIR /srv/www