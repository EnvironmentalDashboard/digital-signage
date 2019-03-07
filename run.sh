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
	docker run -dit -p 5000:80 --restart always -v $(pwd)/var:/var/www/html/var/ --name PROD_DS digital-signage
else
	# dev env:
	# (bind mounting code so changes to code don't require image rebuild)
	docker run -dit -p 5000:80 --restart always -v $(pwd)/var:/var/www/html/var/ -v $(pwd):/var/www/html/ --name DEV_DS digital-signage
fi
