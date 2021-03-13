#!/usr/bin/env bash

while [ true ]
do
  php /var/www/artisan schedule:run --verbose --no-interaction > /var/www/queue.log &
  sleep 60
done

