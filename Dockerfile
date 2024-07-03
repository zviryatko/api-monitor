FROM skilldlabs/php:82 as spx
RUN apk update && apk add php82-dev libzip-dev libxml2-dev openssl git unzip acl zlib-dev autoconf g++ make \
    && cd /opt \
    && wget https://github.com/NoiseByNorthwest/php-spx/archive/refs/heads/master.zip -O php-spx.zip \
    && unzip php-spx.zip \
    && rm php-spx.zip \
    && cd php-spx-master \
    && phpize82 \
    && ./configure --with-php-config=/usr/bin/php-config82 \
    && make \
    && make install

# Use skilldlabs/php:82-unit-dev image as base image and add php pecl memcached extension.
# This image is used for running unit tests.
FROM skilldlabs/php:82-unit-dev
COPY --from=spx /usr/lib/php82/modules/spx.so /usr/lib/php82/modules/spx.so
COPY --from=spx /usr/share/misc/php-spx/assets/web-ui /usr/share/misc/php-spx/assets/web-ui

ARG WWW_DATA_UID=1000
ARG WWW_DATA_GID=1000
# Alter the uid and gid for www-data
RUN if [ "${WWW_DATA_UID}" != "0"]; then apk add shadow && groupmod -g ${WWW_DATA_GID} web-group && usermod --uid ${WWW_DATA_UID} --gid ${WWW_DATA_GID} web-user && apk del shadow; fi

# replace /var/www/html/web to /var/www/html/web/docroot in /var/lib/unit/conf.json
RUN sed -i 's/\/var\/www\/html\/web/\/var\/www\/html\/public/g' /var/lib/unit/conf.json

# Install php pecl memcached extension for alpine linux
RUN apk add --no-cache libmemcached-dev zlib-dev \
    php82-pecl-memcached php82-sodium php82-json \
    # XDEBUG
    && echo "zend_extension=xdebug.so" >> /etc/php82/conf.d/50_xdebug.ini \
    && echo "xdebug.mode=debug" >> /etc/php82/conf.d/50_xdebug.ini \
    && echo "xdebug.client_host=172.17.0.1" >> /etc/php82/conf.d/50_xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /etc/php82/conf.d/50_xdebug.ini \
    && echo "xdebug.serverName=drupal" >> /etc/php82/conf.d/50_xdebug.ini \
    # SPX
    && touch /etc/php82/conf.d/spx.ini \
    && echo 'extension=spx.so' >> /etc/php82/conf.d/spx.ini \
    && echo 'spx.http_enabled=1' >> /etc/php82/conf.d/spx.ini \
    && echo 'spx.http_key=dev' >> /etc/php82/conf.d/spx.ini \
    && echo 'spx.data_dir="/var/www/html/docroot/sites/default/files/spx/"' >> /etc/php82/conf.d/spx.ini \
    && echo 'spx.http_ip_whitelist="*"' >> /etc/php82/conf.d/spx.ini \
    && echo 'spx.http_profiling_enabled=1' >> /etc/php82/conf.d/spx.ini \
    && echo 'spx.http_profiling_auto_start=0' >> /etc/php82/conf.d/spx.ini

RUN git config --global --add safe.directory /var/www/html

ENV PATH="/var/www/html/vendor/bin:${PATH}"
