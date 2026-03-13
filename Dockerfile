# syntax=docker/dockerfile:1

FROM docker.io/library/node:20-bookworm-slim AS frontend-build
WORKDIR /app

COPY package.json pnpm-lock.yaml ./
RUN corepack enable && pnpm install --frozen-lockfile

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN pnpm run build

FROM ghcr.io/serversideup/php:8.5-frankenphp AS base

USER root

RUN install-php-extensions intl sockets gd bcmath

RUN apt-get update && apt-get install -y ffmpeg \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

FROM base AS development

ARG USER_ID
ARG GROUP_ID

RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service frankenphp

USER www-data

FROM base AS app

COPY . .
COPY --from=frontend-build /app/public/build ./public/build

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data
