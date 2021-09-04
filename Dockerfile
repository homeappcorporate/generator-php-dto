FROM php:7.4-cli-alpine3.13
COPY ./ /app/
WORKDIR /app/
RUN  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
     && php composer-setup.php --2 --filename=composer --quiet \
     && php -r "unlink('composer-setup.php');" \
     && ./composer i --no-cache --no-dev --no-progress --optimize-autoloader --classmap-authoritative --quiet --no-interaction \
     && rm -rf composer \
     && ln -s /app/bin/generator /bin/generator
ENTRYPOINT ["generator"]

