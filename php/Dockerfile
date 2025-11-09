ARG BASE_IMAGE=php:8.2-fpm-alpine
FROM ${BASE_IMAGE}

# Install dependencies
RUN apk add --no-cache \
    curl wget zip unzip git \
    freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev icu-dev oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd pdo pdo_mysql mysqli zip intl mbstring opcache bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Configure PHP-FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' \
    /usr/local/etc/php-fpm.d/www.conf

# Copy custom PHP configuration
COPY php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/html

# Create non-root user
RUN addgroup -g 1000 appuser && \
    adduser -D -u 1000 -G appuser appuser && \
    chown -R appuser:appuser /var/www

# Switch to non-root user
USER appuser

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD pidof php-fpm || exit 1

EXPOSE 9000
CMD ["php-fpm"]
