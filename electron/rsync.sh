#!/bin/bash

# upload dist files
rsync -avzh --progress --stats ./dist/ root@159.89.233.159:/var/www/repos/digital-signage/electron/dist/