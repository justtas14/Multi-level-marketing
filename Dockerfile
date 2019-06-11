FROM php:7.2.5-apache
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libxrender1 \
        libfontconfig1 \
        libxtst6 \
        zip \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql
ARG app_env=''
ARG mailer_encryption=''
RUN a2enmod rewrite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN sed -i 's@html@html/public@g' /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\SetEnv APP_ENV $app_env" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\SetEnv MAILER_ENCRYPTION $mailer_encryption" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\PassEnv MAILER_URL" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\PassEnv DATABASE_URL" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\PassEnv INVITATION_SENDER" /etc/apache2/sites-available/000-default.conf
COPY ./ /var/www/html
RUN chmod -R 777 /var/www/html/var
RUN chmod -R 777 /var/www/html/public/files