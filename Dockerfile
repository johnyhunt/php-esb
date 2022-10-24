FROM php:8.1.1-cli

RUN apt-get update && apt-get install vim -y && \
    apt-get install apt-utils -y && \
    apt-get install openssl -y && \
    apt-get install libssl-dev -y && \
    apt-get install wget -y && \
    apt-get install git -y && \
    apt-get install procps -y && \
    apt-get install htop -y

RUN apt-get install -y libpq-dev &&\
    apt-get install -y postgresql-client &&\
    docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

ADD . .

RUN composer install -o -n --no-progress

RUN chmod a+x /app/entrypoint.sh

CMD ["php", "-S 0.0.0.0:8080 -t example"]

ENTRYPOINT ["sh", "/app/entrypoint.sh"]

EXPOSE 8080
