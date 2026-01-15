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

# 6. Habilitar mod_rewrite
RUN a2enmod rewrite

# 7. Configurar pasta
WORKDIR /var/www/html

# 8. Copiar arquivos do projeto
COPY . /var/www/html

# 9. Instalar Composer e dependências
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 10. Frontend Build
RUN npm install
RUN npm run build

# 11. Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Copia o seu arquivo 000-default.conf para dentro do Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 13. Garante que o Apache 'leia' essa variável de ambiente corretamente
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# 14. Expor porta
EXPOSE 80

# 15. Iniciar o Apache e rodar comandos do Laravel
CMD php artisan optimize:clear && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan migrate --force && \
    apache2-foreground