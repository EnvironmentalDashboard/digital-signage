#!/bin/bash

docker run -dit --name release-server -p 1993:80 -v ./dist:/usr/local/apache2/htdocs/ httpd:2.4