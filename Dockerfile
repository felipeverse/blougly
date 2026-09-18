# syntax=docker/dockerfile:1.4

FROM php:8.3-cli-alpine AS base
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /blougly

# Stage: production
FROM base AS production
COPY composer.json composer.lock* ./
COPY entrypoint.sh /usr/local/bin/entrypoint.sh                                                                                                               
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "bin/console.php"]

# Stage: dev
FROM base AS dev
RUN apk add --no-cache git bash zsh 
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS linux-headers \
    && { \
        echo "xdebug.mode=debug,coverage"; \
        echo "xdebug.start_with_request=trigger"; \
        echo "xdebug.client_host=127.0.0.1"; \
        echo "xdebug.client_port=9003"; \
        echo "xdebug.idekey=VSCODE"; \
    } >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
ENV COMPOSER_HOME=/root/.composer
RUN composer global require squizlabs/php_codesniffer --no-interaction
WORKDIR /blougly
