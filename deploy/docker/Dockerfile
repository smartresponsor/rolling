FROM composer:2 AS composer-bin

FROM node:22-bookworm-slim AS node-bin

FROM php:8.4-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev libsqlite3-dev libnss3-tools ca-certificates \
    && docker-php-ext-install pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer-bin /usr/bin/composer /usr/local/bin/composer
COPY --from=node-bin /usr/local/bin/node /usr/local/bin/node
COPY --from=node-bin /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /app

COPY deploy/docker/entrypoint.sh /usr/local/bin/rolling-entrypoint
RUN chmod +x /usr/local/bin/rolling-entrypoint

EXPOSE 8000

ENTRYPOINT ["rolling-entrypoint"]
CMD ["php", "tools/qa/current-summary.php"]
