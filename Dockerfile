FROM node:22 as front-builder
WORKDIR /app

# Copy package manager files, and vendor because that way laravel-mix knows that it's laravel
COPY package.json package-lock.json webpack.mix.js tailwind.config.js .babelrc postcss.config.js artisan ./
RUN npm ci

COPY resources ./resources
RUN npm run production

FROM composer:2 as composer-bin

# Build composer install/dump-autoload under the SAME PHP version as the final
# runtime image below, instead of composer:2's own bundled PHP (its floating
# tag has drifted well past what composer.json declares, which silently broke
# builds - see git history for details). Un-comment --ignore-platform-reqs
# again only if you have a specific reason composer's own version check is
# wrong; it was previously used to paper over exactly this kind of drift.
FROM php:8.1-cli as back-builder
COPY --from=composer-bin /usr/bin/composer /usr/bin/composer
# maatwebsite/excel's phpoffice/phpspreadsheet dependency declares ext-gd as
# required (used for chart/image handling in some export formats); without it
# here composer's own platform check correctly refuses to install.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions gd zip
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-ansi \
    --no-autoloader \
    --no-dev \
    --no-interaction \
    --no-scripts

COPY . .
RUN composer dump-autoload -a

# Build app image
FROM php:8.1-apache

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions opcache pgsql pdo_pgsql bcmath mysqli pdo_mysql pcntl gd zip

RUN a2enmod rewrite

COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php.ini ${PHP_INI_DIR}/conf.d/99-overrides.ini
COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh

WORKDIR /app
COPY --chown=www-data:www-data --from=back-builder /app/ /app
COPY --chown=www-data:www-data --from=front-builder /app/public/ /app/public

VOLUME /app/storage/logs
VOLUME /app/storage/app

# Cache everything except config cache because .env is loaded at container creation time.
USER www-data
RUN php artisan route:cache \
 && php artisan event:cache \
 && php artisan view:cache
USER root

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
