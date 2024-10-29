#!/bin/bash
if [! -f 'vender/autoload.php']; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi
