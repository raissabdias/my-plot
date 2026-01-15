# 1. Usar a imagem oficial do PHP 8.4
FROM php:8.4-apache

# 2. Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# 3. Instalar Node.js (Versão 20)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# 4. Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 5. Instalar extensões do PHP
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# 6. Habilitar mod_rewrite (Essencial para Laravel)
RUN a2enmod rewrite

# 7. Configurar pasta
WORKDIR /var/www/html

# 8. Copiar arquivos
COPY . /var/www/html

# 9. Instalar Composer e dependências
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 10. Frontend Build
RUN npm install
RUN npm run build

# 11. Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Configurar Apache "Na marra"
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Cria um arquivo de configuração do site que permite .htaccess (AllowOverride All)
RUN echo "<VirtualHost *:80>" > /etc/apache2/sites-available/000-default.conf && \
    echo "    DocumentRoot ${APACHE_DOCUMENT_ROOT}" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    <Directory ${APACHE_DOCUMENT_ROOT}>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        Options Indexes FollowSymLinks" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        AllowOverride All" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        Require all granted" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    </Directory>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    ErrorLog \${APACHE_LOG_DIR}/error.log" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    CustomLog \${APACHE_LOG_DIR}/access.log combined" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</VirtualHost>" >> /etc/apache2/sites-available/000-default.conf


# 13. Expor porta
EXPOSE 80

# 14. Iniciar
CMD php artisan optimize:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    apache2-foreground