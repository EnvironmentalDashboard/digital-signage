#!/bin/bash

# Prepare a FQDN into a domain name.
# On Linux, dnsdomainname can be used,
# but using cut allows for backwards-compatibility
# with Mac OS.
production_domain=environmentaldashboard.org
domain=`cut -f 2- -d . <<< $HOSTNAME`

if [ "$domain" = "$production_domain" ] || [ "$HOSTNAME" = "$production_domain" ]
then
	# prod env:
	docker run -dit -p 5000:80 -p 5001:8080 --restart always -v /var/www/uploads/digital-signage:/var/www/html/public/uploads -e APP_ENV=prod --name PROD_DS digital-signage
else
	# dev env:
	# (bind mount code so changes to code don't require image rebuild, bind mount sqlite db so data persists across rebuilds)
	docker run -dit -p 5000:80 -p 5001:8080 --restart always -v $(pwd)/uploads:/var/www/html/public/uploads -v $(pwd)/var:/var/www/html/var/ -v $(pwd):/var/www/html/ -e APP_ENV=dev --name DEV_DS digital-signage
fi
