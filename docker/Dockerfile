FROM php:8.1-fpm

RUN apt-get update && apt-get install -y nginx supervisor

COPY ./ /var/www/html/

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
