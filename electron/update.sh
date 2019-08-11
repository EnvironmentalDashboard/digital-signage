#!/bin/bash

curl -O https://releases.communityhub.cloud/latest.yml > /dev/null
cmp latest.yml current.yml > /dev/null
if [ $? -gt 0 ] # if there's a new version
then
    echo "Downloading new release"
    rm *.deb # delete old versions
    curl -O https://releases.communityhub.cloud/latest.deb
    echo "Installing new version"
    pkill media-player # kill running processes
    sudo dpkg -r media-player # remove package
    sudo dpkg -i latest.deb # reinstall debian package
    media-player # start player
fi