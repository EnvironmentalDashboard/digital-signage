#!/bin/bash

docker build --build-arg CMD="/usr/sbin/apache2ctl -D FOREGROUND" -t digital-signage .
docker build --build-arg CMD="php bin/console ws-server:start" -t digital-signage-wsserver .