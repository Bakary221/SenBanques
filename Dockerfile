# Utiliser l'image officielle PHP avec Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires pour Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql pdo_mysql zip bcmath

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Créer un .env si inexistant
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Installer les dépendances PHP (inclut Faker pour les seeders)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-interaction

# Installer Faker globalement si nécessaire
RUN COMPOSER_ALLOW_SUPERUSER=1 composer require fakerphp/faker --no-interaction

# Générer la clé d'application Laravel
RUN php artisan key:generate --force

# Créer le répertoire oauth dans storage
RUN mkdir -p storage/oauth

# Créer le lien symbolique du storage si nécessaire
RUN php artisan storage:link || true

# Installer Node.js et les dépendances front-end (si nécessaire)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Définir le DocumentRoot vers le dossier public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Changer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Activer mod_rewrite pour Laravel
RUN a2enmod rewrite

# Configurer Apache pour permettre l'utilisation des .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel

# Exposer le port 80
EXPOSE 80

# Démarrer Apache et exécuter les migrations/fresh + seeds + Passport keys au runtime
CMD mkdir -p storage/oauth && php artisan migrate:fresh --force && php artisan passport:install --force && php artisan passport:keys --force && php artisan db:seed --force && apache2-foreground
