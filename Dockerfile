FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html/

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html

RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:${PORT}>/' /etc/apache2/sites-available/000-default.conf

ENV PORT=10000

EXPOSE 10000

CMD ["sh", "-c", "sed -i \"s/\\${PORT}/$PORT/g\" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
