#!/bin/bash

scp root@159.89.232.129:/var/www/uploads/digital-signage/* ./public/uploads/
# keep column-statistics=0, see https://serverfault.com/a/912677/456938
mysqldump --column-statistics=0 --compact --skip-extended-insert -h 159.89.232.129 -u digital_signage -p digital_signage > dump.sql
php bin/console doctrine:database:drop --force
rm src/Migrations/*.php
php bin/console doctrine:database:create
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
rm ./var/data.db
touch ./var/data.db
./mysql2sqlite dump.sql | sqlite3 ./var/data.db # this tool is from https://github.com/dumblob/mysql2sqlite