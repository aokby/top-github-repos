FROM php:7.4-fpm

RUN apt-get update 
RUN apt-get install -y --force-yes unzip git curl
RUN apt-get install -y --force-yes acl

RUN cd ~ \
      && curl -sS https://getcomposer.org/installer -o composer-setup.php \ 
      && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]