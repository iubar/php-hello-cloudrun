# Usa l'immagine ufficiale di PHP con Apache
FROM php:8.2-apache

# Abilita mod_rewrite per Apache (utile per molti progetti PHP)
RUN a2enmod rewrite

# Modifica la configurazione di Apache per usare la porta 8080 (richiesto da Cloud Run)
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf && \
    sed -i 's/:80/:8080/' /etc/apache2/sites-available/000-default.conf
	
# Abilita mod_rewrite e altre estensioni se necessarie (es. PDO MySQL e mbstring)
RUN docker-php-ext-install pdo pdo_mysql

# Configura Apache per usare .htaccess (se ti serve)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

	
# Installa Composer globalmente
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# ...oppure in alterantiva...

# Installa strumenti richiesti (wget, unzip)
RUN apt-get update && apt-get install -y wget unzip git \
    && rm -rf /var/lib/apt/lists/*

# Installa Composer manualmente
RUN wget https://getcomposer.org/installer -O composer-setup.php \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php
 		
# Imposta la directory del progetto
WORKDIR /var/www/html
			
# Copia i file del progetto nella directory di Apache
# COPY . /var/www/html/
COPY . .

# Installa le dipendenze via Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Imposta i permessi (opzionale ma utile)
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# (Opzionale) Imposta la variabile d'ambiente che Apache usa per la porta
ENV PORT 8080

# Espone la porta 8080 per Cloud Run
EXPOSE 8080

# Usa lo script di avvio
# CMD ["start-apache.sh"]