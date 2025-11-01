# Utiliser l'image officielle PHP avec Apache
FROM php:8.3-apache

# Installer les extensions PHP nécessaires pour Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer Apache
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY . /var/www/html

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-dev

# Publier les vues et assets Swagger
RUN php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --force && \
    php artisan vendor:publish --tag=l5-swagger-assets --force

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copier la configuration Apache personnalisée
COPY <<EOF /etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Créer le script de démarrage
RUN echo '#!/bin/bash\n\
# Générer la clé d application si elle n existe pas\n\
if [ -z "$APP_KEY" ]; then\n\
    php artisan key:generate\n\
fi\n\
\n\
# Attendre que la base de données soit prête (avec timeout)\n\
echo "Waiting for database..."\n\
timeout=60\n\
counter=0\n\
while ! php artisan migrate:status > /dev/null 2>&1; do\n\
    if [ $counter -ge $timeout ]; then\n\
        echo "Database connection timeout, starting Apache anyway..."\n\
        break\n\
    fi\n\
    echo "Database not ready, waiting..."\n\
    sleep 2\n\
    counter=$((counter + 2))\n\
done\n\
\n\
# Exécuter les migrations si la DB est accessible\n\
if php artisan migrate:status > /dev/null 2>&1; then\n\
    echo "Running migrations..."\n\
    php artisan migrate --force\n\
    echo "Migrations completed successfully"\n\
else\n\
    echo "Skipping migrations due to database issues"\n\
fi\n\
\n\
# Générer la documentation Swagger\n\
php artisan l5-swagger:generate\n\
\n\
# Démarrer Apache\n\
apache2-foreground' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["/usr/local/bin/start.sh"]