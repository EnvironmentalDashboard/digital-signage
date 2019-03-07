# digital-signage


After cloning this repository run `./build/composer-install.sh && php composer.phar install && php bin/console doctrine:database:create && php bin/console doctrine:migrations:migrate` to create the database which needs to reside on the host and be bind mounted into the container so data persists across rebuilds.