FROM php:8-cli

ARG APP_ENV

ENV USER=user
ENV UID=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip

## Create system user to run Composer
RUN useradd -G www-data,root -u $UID -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER;

# Create and set working directory
RUN mkdir /app; \
    chown -R $USER:$USER /app; \
    chmod 755 /app;
WORKDIR /app

# Copy files
COPY --chown=$USER:$USER --chmod=755 . /app/

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install dependencies
RUN if [ "${APP_ENV}" = "production" ]; then \
      composer install --no-dev --optimize-autoloader; \
    else \
      composer install --optimize-autoloader; \
    fi;

CMD if [ "${APP_ENV}" = "production" ]; then \
      php ./run.php input.txt; \
    else \
      php ./vendor/bin/phpunit --display-warnings; \
    fi;

USER $USER
