#!/bin/bash
sudo apt-get install -y nginx php5 php5-cli php5-fpm git

sudo rm /etc/nginx/sites-enabled/default

cd ~
git clone https://github.com/Verber/cee-oss-azure-homework.git
cd ~/cee-oss-azure-homework
./composer.phar install
sudo cp ./scripts/silex.conf /etc/nginx/sites-available/
cd /etc/nginx/sites-enabled/
sudo ln -s ../sites-available/silex.conf

sudo service nginx start
sudo service php5-fpm start


