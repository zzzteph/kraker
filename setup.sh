#!/bin/bash
sudo apt update
sudo apt-get -y install unzip git
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
rm get-docker.sh
sudo curl -L "https://github.com/docker/compose/releases/download/1.25.3/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
sudo systemctl enable docker

git clone https://github.com/zzzteph/kraker
cd kraker/server
sudo docker-compose build app
sudo docker-compose up -d
sudo docker-compose exec app composer install
sudo docker-compose exec app php artisan key:generate
sudo docker-compose exec app php artisan migrate
sudo docker-compose exec app php artisan db:seed --class=HashtypeSeeder
sudo docker-compose exec app php artisan db:seed --class=UserSeeder
