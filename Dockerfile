FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libsqlite3-dev \
        libonig-dev \
        && docker-php-ext-install pdo pdo_mysql pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . /app

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
