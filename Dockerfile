# Use the official PHP image as the base image
FROM dunglas/frankenphp:php8.4-alpine

# Update package lists and install required dependencies
# Install necessary packages and dependencies
RUN apk add --no-cache --update \
    git \
    unzip \
    libssl3 \
    nss \
    nss-tools \
    icu-libs \
    icu-dev \
    libzip \
    libzip-dev \
    oniguruma-dev \
    curl \
    build-base \
    autoconf \
    bash \
    openssl-dev \
    postgresql-dev \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    ctype \
    mbstring \
    pdo \
    intl \
    pdo_pgsql

# Install PHP extension OPcache (PHP accelerator)
RUN docker-php-ext-install opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Statamic CLI globally
RUN composer global require statamic/cli
#RUN composer global config bin-dir --absolute
#RUN export PATH="$PATH:$(composer global config bin-dir --absolute)"
#RUN echo 'export PATH="$PATH:$(composer global config bin-dir --absolute)"' >> ~/.bashrc
#ENV PATH="/root/.composer/vendor/bin:$PATH"

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Set recommended PHP.ini settings for Symfony
ARG TIMEZONE=UTC
RUN echo "date.timezone = $TIMEZONE" >> /usr/local/etc/php/php.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/php.ini \
    && echo "short_open_tag = Off" >> /usr/local/etc/php/php.ini

# Configure OPcache
ARG ENABLE_OPCACHE=0
RUN if [ "$ENABLE_OPCACHE" = "1" ]; then \
        echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini; \
        echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini; \
        echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini; \
        echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini; \
        echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini; \
        mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    else \
      echo "opcache.enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini; \
    fi

# Install and configure Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ARG SERVER_NAME=localhost
ENV SERVER_NAME=${SERVER_NAME}
