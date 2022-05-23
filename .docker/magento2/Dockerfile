FROM php:7-apache

# set PATH for composer binaries
ENV PATH="~/.composer/vendor/bin:${PATH}"

# reduce APT noise
ENV DEBIAN_FRONTEND=noninteractive

# use proper shell
SHELL ["/bin/bash", "-c"]

# to avoid all too common aborts because of debian repo timeouts
RUN echo 'APT::Acquire::Retries "30";' > /etc/apt/apt.conf.d/80-retries

# upgrade package list and default packages
RUN apt-get -qq update
RUN apt-get -qq upgrade

# install npm nodesource repo
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -

# install dependencies aand tools
RUN apt-get -qq install git unzip vim mariadb-client zip jq nodejs

# install php extension dependencies
RUN apt-get -qq install libmemcached-dev libzip-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev libonig-dev libtidy-dev libicu-dev libxml2-dev libxslt-dev

# clean up to reduce docker image size
RUN apt-get -qq autoremove

# install PHP extensions required
RUN bash -c "pecl install xdebug memcached &> /dev/null"
RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp
RUN docker-php-ext-install -j64 intl sockets soap gd mbstring mysqli pdo pdo_mysql tidy bcmath xsl zip
RUN docker-php-ext-enable intl sockets xsl zip memcached xdebug gd mbstring mysqli pdo pdo_mysql tidy bcmath

# enable apache modules
RUN a2enmod rewrite headers ext_filter expires

# create self-signed cert and enable SSL on apache
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem -subj "/C=AT/ST=Vienna/L=Vienna/O=Security/OU=Development/CN=example.com"
RUN a2ensite default-ssl
RUN a2enmod ssl

# get composer binary from composer docker image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# add user and dir for executing composer
RUN useradd -u 431 -r -g www-data -s /sbin/nologin -c "magento user" magento

# set permissions for magento user
RUN mkdir -p /home/magento && chown -R magento:www-data /home/magento /etc/ssl /var/www

# install ngrok
COPY --from=ngrok/ngrok:debian /bin/ngrok /usr/bin/ngrok

# magento is greedy
RUN echo memory_limit=4G > /usr/local/etc/php/conf.d/give_me_more_memory__give_me_MOOOORE.ini

# continue as user for correct permissions
USER magento

# clone magento2 base and sample data
# checkout all branches to have them in the image to speed up checkout in entrypoint
RUN git clone https://github.com/magento/magento2 /var/www/magento2
RUN cd /var/www/magento2 && for BRANCH in $(git branch -a | grep remotes | grep -v HEAD | grep -v master); do git branch --track "${BRANCH#remotes/origin/}" "${BRANCH}"; done
RUN git clone https://github.com/magento/magento2-sample-data /var/www/magento2/magento2-sample-data
RUN cd /var/www/magento2/magento2-sample-data && for BRANCH in $(git branch -a | grep remotes | grep -v HEAD | grep -v master); do git branch --track "${BRANCH#remotes/origin/}" "${BRANCH}"; done

# copy entrypoint script
COPY init.sh /usr/local/bin/init.sh

# copy ngrok script
COPY ngrok.sh /usr/local/bin/ngrok.sh

# copy plugin
RUN mkdir /tmp/plugin
COPY . /tmp/plugin/

# make scripts executable
USER root
RUN chmod +x /usr/local/bin/*.sh

WORKDIR /var/www/html
USER magento

# override default entrypoin with ours
ENTRYPOINT [ "init.sh" ]

EXPOSE 80
EXPOSE 443
