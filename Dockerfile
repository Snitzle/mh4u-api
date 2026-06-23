# syntax=docker/dockerfile:1

# Local-development container for the MH4U Database API (Laravel 13 on PHP 8.4).
#
# The application code is bind-mounted at runtime (see docker-compose.yml), so
# this image only provides the PHP runtime, the extensions Laravel + Composer
# need, and Composer itself. First-run setup (install, migrate, seed, assets,
# docs) and serving happen in docker-entrypoint.sh.
FROM php:8.4-cli

# System libraries required to build the PHP extensions below.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
        libsqlite3-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_sqlite zip intl bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer (pinned to v2) from the official image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Lives outside the bind-mounted working directory so it is never shadowed.
COPY docker-entrypoint.sh /usr/local/bin/mh4u-entrypoint
RUN chmod +x /usr/local/bin/mh4u-entrypoint

WORKDIR /var/www/html

# `php artisan serve` listens here; docker-compose maps it to the host.
EXPOSE 8088

ENTRYPOINT ["/usr/local/bin/mh4u-entrypoint"]
