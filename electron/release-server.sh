#!/bin/bash

docker build -f ./release-server.Dockerfile -t release-server . && \
docker run -dit --name release-server -p 1993:80 release-server