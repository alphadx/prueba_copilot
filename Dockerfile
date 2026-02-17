FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libsqlite3-dev \
    git \
    unzip \
    sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    mbstring \
    intl \
    gd \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Make setup script executable
RUN chmod +x setup.sh

# Expose port
EXPOSE 8080

# Run setup and start PHP built-in server
CMD ["sh", "-c", "./setup.sh && php -S 0.0.0.0:8080 -t sgdii-tesis/web"]
