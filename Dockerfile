FROM php:7.2.5-apache
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd
ARG db_url=''
ARG mailer_url=''
ARG app_env=''
RUN apt-get install zip -y
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN sed -i 's@html@html/public@g' /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\SetEnv APP_ENV $app_env" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\SetEnv MAILER_URL $mailer_url" /etc/apache2/sites-available/000-default.conf
RUN sed -i "/<\/VirtualHost>/ i\SetEnv DATABASE_URL $db_url" /etc/apache2/sites-available/000-default.conf
RUN apt-get install libxrender1 -y
RUN apt-get install libfontconfig1 -y
RUN apt-get install libxtst6 -y
RUN touch /var/log/xdebug.log
RUN chown www-data /var/log/xdebug.log
COPY ./ /var/www/html
RUN chmod -R 777 /var/www/html/var