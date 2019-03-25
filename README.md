# digital-signage


After cloning this repository run `./build/composer-install.sh && php composer.phar install && php bin/console doctrine:database:create && php bin/console doctrine:migrations:migrate` to create the database which needs to reside on the host and be bind mounted into the container so data persists across rebuilds.

To fill the database with some test data, run `cat ./build/default-templates.sql | sqlite3 ./var/data.db` and `cat ./build/test-data.sql | sqlite3 ./var/data.db`