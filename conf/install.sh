#/bin/bash
apt-get update
apt-get upgrade
apt-get install apache2 php5 php5-sqlite php5-curl php5-mcrypt
rm /var/www/html/index.php
a2enmod rewrite
php5enmod mcrypt
cp /config/apache2.conf /etc/apache2/apache2.conf
cp /config/php.ini /etc/php5/apache2/php.ini
cp /config/000-default.conf /etc/apache2/sites-available/000-default.conf
#Add the https version here + all the ssl generation etc
service apache2 start
