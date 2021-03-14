#!/bin/bash

sh /var/www/queue.sh & docker-php-entrypoint php-fpm
