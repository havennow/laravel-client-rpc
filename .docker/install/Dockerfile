FROM --platform=linux/amd64 php:8.4-cli-alpine

ARG USER=lcr
ARG UID=1000
ARG GID=1000

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1"
ENV NODE_VERSION 14.19.0

RUN ln -fs /usr/share/zoneinfo/UTC /etc/localtime

RUN addgroup ${USER}

RUN adduser -D -G ${USER} -u ${UID} ${USER}

RUN apk update && apk add \
      iputils \
      libzip-dev \
      gd-dev \
      geoip-dev \
      git \
      gnupg \
      imagemagick \
      jpegoptim \
      libffi-dev \
      libgit2 \
      libxslt-dev \
      make \
      openssh \
      openssl-dev \
      optipng \
      pcre-dev \
      pngquant \
      unzip \
      wget \
      zip \
      zlib-dev \
      curl \
      curl-dev \
      libpq \
      libevent \
      libevent-dev \
      zeromq \
      bash

# Swoole

RUN  apk add --no-cache --virtual .build-deps \
      autoconf \
      $PHPIZE_DEPS \
      automake \
      g++ \
      gcc \
      gettext \
      libc-dev \
      libtool \
      linux-headers \
      musl-dev \
      perl-dev \
      zlib-dev \
      icu-dev \
      c-ares \
      c-ares-dev

# Pcntl
RUN docker-php-ext-install pcntl

# intl
RUN docker-php-ext-configure intl && docker-php-ext-install intl \
    && docker-php-ext-enable intl

# php sockets
RUN docker-php-ext-configure sockets
RUN docker-php-ext-install sockets
RUN docker-php-ext-enable sockets

# Swoole
RUN pecl install --configureoptions 'enable-debug="yes" enable-debug-log="yes" enable-sockets="yes" enable-openssl="yes" enable-http2="yes" enable-swoole-json="yes" enable-swoole-curl="yes" enable-cares="yes"' openswoole && \
docker-php-ext-enable openswoole --ini-name zz-swoole.ini openswoole

# php libevent

RUN apk add --update-cache --no-cache autoconf gcc coreutils build-base git && \
  git clone https://bitbucket.org/osmanov/pecl-event.git  && \
  cd pecl-event && \
  phpize && \
  ./configure --with-event-extra && \
  make && \
  make install && \
  docker-php-ext-enable event && \
  mv /usr/local/etc/php/conf.d/docker-php-ext-event.ini \
  		/usr/local/etc/php/conf.d/docker-php-ext-zz-event.ini

# php msgpack
RUN pecl install msgpack
RUN docker-php-ext-enable msgpack
# mssql odbc for dabase connection

#Download the desired package(s)
RUN curl -O https://download.microsoft.com/download/b/9/f/b9f3cce4-3925-46d4-9f46-da08869c6486/msodbcsql18_18.0.1.1-1_amd64.apk \
	&& curl -O https://download.microsoft.com/download/b/9/f/b9f3cce4-3925-46d4-9f46-da08869c6486/mssql-tools18_18.0.1.1-1_amd64.apk


#(Optional) Verify signature, if 'gpg' is missing install it using 'apk add gnupg':
RUN curl -O https://download.microsoft.com/download/b/9/f/b9f3cce4-3925-46d4-9f46-da08869c6486/msodbcsql18_18.0.1.1-1_amd64.sig \
	&& curl -O https://download.microsoft.com/download/b/9/f/b9f3cce4-3925-46d4-9f46-da08869c6486/mssql-tools18_18.0.1.1-1_amd64.sig

RUN curl https://packages.microsoft.com/keys/microsoft.asc  | gpg --import - \
	&& gpg --verify msodbcsql18_18.0.1.1-1_amd64.sig msodbcsql18_18.0.1.1-1_amd64.apk \
	&& gpg --verify mssql-tools18_18.0.1.1-1_amd64.sig mssql-tools18_18.0.1.1-1_amd64.apk

#Install the package(s)
RUN apk add --allow-untrusted msodbcsql18_18.0.1.1-1_amd64.apk \
	&& apk add --allow-untrusted mssql-tools18_18.0.1.1-1_amd64.apk \
	&& rm -f msodbcsql18_18.0.1.1-1_amd64.apk mssql-tools18_18.0.1.1-1_amd64.apk

RUN apk add --update-cache --no-cache freetds unixodbc \
    && apk add autoconf $PHPIZE_DEPS \
        unixodbc-dev \
        freetds-dev \
    && docker-php-source extract \
    && docker-php-ext-install pdo_dblib \
    && pecl install \
        sqlsrv \
        pdo_sqlsrv \
    && docker-php-ext-enable --ini-name 30-sqlsrv.ini sqlsrv \
    && docker-php-ext-enable --ini-name 35-pdo_sqlsrv.ini pdo_sqlsrv \
    && docker-php-source delete

# php zeromq
RUN apk add --update-cache --no-cache libzmq zeromq-dev zeromq && \
  apk add --update-cache --no-cache --virtual=.build-php-dependencies \
  autoconf gcc coreutils build-base git && \
  git clone https://github.com/mkoppanen/php-zmq.git && \
  cd php-zmq && \
  phpize && \
  ./configure && \
  make && \
  make install && \
  docker-php-ext-enable zmq

RUN docker-php-ext-enable opcache && \
      docker-php-ext-install zip && \
      docker-php-ext-install soap && \
      docker-php-ext-install calendar


#imagick
RUN apk add --update imagemagick imagemagick-libs imagemagick-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# php exif
RUN docker-php-ext-configure exif \
&& docker-php-ext-install exif \
&& docker-php-ext-enable exif


# php gd
RUN apk add --no-cache \
      freetype \
      libjpeg-turbo \
      libpng \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      # --with-png=/usr/include/ \ # No longer necessary as of 7.4; https://github.com/docker-library/php/pull/910#issuecomment-559383597
      --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk del --no-cache \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && rm -rf /tmp/*

#RUN apk add --no-cache nodejs=${NODE_VERSION}-r0 npm yarn
RUN apk add --no-cache nodejs npm yarn
RUN apk update && apk add yarn

# Install chokidar
RUN yarn global add chokidar

WORKDIR /var/www/

# Get latest Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN chown -R ${USER}:${USER} /usr/bin/composer
RUN chown -R ${USER}:${USER} /var/www/

USER $USER
