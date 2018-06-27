#!/bin/bash

./shell/update_permissions.sh

#linux
composer install

#mac
$(which composer.phar) install

./shell/update_db.sh

bin/console cache:clear --no-warmup --env=prod
bin/console cache:warmup --env=prod

