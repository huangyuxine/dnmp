ARG PHP_VERSION
FROM ${PHP_VERSION}

ARG COMPOSER_URL
ARG TZ
ARG PHP_EXTENSIONS

RUN apk --no-cache add tzdata \
    && cp "/usr/share/zoneinfo/$TZ" /etc/localtime \
    && echo "$TZ" > /etc/timezone

#安装扩展
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions "${PHP_EXTENSIONS}"


#composer
RUN curl -o /usr/bin/composer https://${COMPOSER_URL}/composer/composer.phar \
    && chmod +x /usr/bin/composer
ENV COMPOSER_HOME=/tmp/composer


WORKDIR /www