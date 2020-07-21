#!/bin/bash
folder=$1

sudo mkdir /var/www/"$folder"/data
sudo chown www-data:www-data /var/www/"$folder"/data
sudo chmod 777 /var/www/"$folder"/data

sudo touch /var/www/"$folder"/data/data.csv
sudo touch /var/www/"$folder"/data/_data.csv
sudo touch /var/www/"$folder"/data/coronavirus-data.csv

sudo chmod -R 777 /var/www/"$folder"/data
sudo chown -R www-data:www-data /var/www/"$folder"/data