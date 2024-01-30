ARG PHP_VERSION
FROM ${PHP74_VERSION}

ARG TZ
ARG PHP_EXTENSIONS

RUN apk --no-cache add tzdata \
    && cp "/usr/share/zoneinfo/$TZ" /etc/localtime \
    && echo "$TZ" > /etc/timezone

#安装扩展
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions "${PHP_EXTENSIONS}"

WORKDIR /www