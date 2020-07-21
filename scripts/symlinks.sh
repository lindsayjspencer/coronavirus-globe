#!/bin/bash
folder=$1

sudo mkdir /var/www/"$folder"/assets/vendor/tween.js
sudo chown www-data:www-data /var/www/"$folder"/assets/vendor/tween.js
sudo chmod 777 /var/www/"$folder"/assets/vendor/tween.js
sudo ln -s /var/www/"$folder"/node/node_modules/tween.js/src/Tween.js /var/www/"$folder"/assets/vendor/tween.js/tween.js

sudo ln -s /var/www/"$folder"/node/node_modules/three/ /var/www/"$folder"/assets/vendor/three
sudo ln -s /var/www/"$folder"/node/node_modules/jquery/dist/ /var/www/"$folder"/assets/vendor/jquery
sudo ln -s /var/www/"$folder"/node/node_modules/bootstrap/dist/ /var/www/"$folder"/assets/vendor/bootstrap
sudo ln -s /var/www/"$folder"/node/node_modules/@popperjs/core/dist/esm/ /var/www/"$folder"/assets/vendor/popperjs

sudo chmod -R 777 /var/www/"$folder"/assets
sudo chown -R www-data:www-data /var/www/"$folder"/assets