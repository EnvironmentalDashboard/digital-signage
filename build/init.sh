#!/bin/bash

# install apt packages, set timezone (see: https://serverfault.com/a/683651/456938), install composer
apt-get update
apt-get -qq -y install apt-utils tzdata apache2 php libapache2-mod-php php-cli php-mbstring php-xml curl php-curl php-xdebug php-gd unzip sqlite php-sqlite3 php-mysql

INI_LOC=`php -i | grep 'Loaded Configuration File => ' | sed 's/Loaded Configuration File => //g' | sed 's/cli/apache2/g'`
sed -ie 's/upload_max_filesize = 2M/upload_max_filesize = 64M/g' "$INI_LOC"
sed -ie 's/post_max_size = 8M/post_max_size = 512M/g' "$INI_LOC"

ln -snf /usr/share/zoneinfo/$TZ /etc/localtime
echo $TZ > /etc/timezone

./build/composer-install.sh
php composer.phar install
php composer.phar update
php composer.phar dump-env prod
phpdismod xdebug
a2enmod rewrite headers
mv /var/www/html/apache/000-default.conf /etc/apache2/sites-available/000-default.conf