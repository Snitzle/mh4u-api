# syntax=docker/dockerfile:1

# Local-development container for the MH4U Database API (Laravel 13 on PHP 8.4),
# served by Apache against a MySQL container. The application code is
# bind-mounted at runtime (see docker-compose.yml); this image provides the PHP
# runtime, extensions, Composer and the Apache vhost. First-run setup (install,
# migrate, seed, assets) and serving happen in docker-entrypoint.sh.
FROM php:8.4-apache

# System libraries for the PHP extensions + the mysql client (used by the
# `mh4u:export-sql` dump command).
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        default-mysql-client \
        libzip-dev \
        libicu-dev \
        libsqlite3-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_sqlite zip intl bcmath \
    && pecl install redis pcov \
    && docker-php-ext-enable redis pcov \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Serve Laravel's public/ directory and honour its .htaccess rewrites.
RUN { \
        echo '<VirtualHost *:80>'; \
        echo '    DocumentRoot /var/www/html/public'; \
        echo '    <Directory /var/www/html/public>'; \
        echo '        AllowOverride All'; \
        echo '        Require all granted'; \
        echo '    </Directory>'; \
        echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
        echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
        echo '</VirtualHost>'; \
    } > /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# Composer (pinned to v2) from the official image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Lives outside the bind-mounted working directory so it is never shadowed.
COPY docker-entrypoint.sh /usr/local/bin/mh4u-entrypoint
RUN chmod +x /usr/local/bin/mh4u-entrypoint

WORKDIR /var/www/html

# Apache listens on 80; docker-compose maps it to host 8088.
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/mh4u-entrypoint"]
