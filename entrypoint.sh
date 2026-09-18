#!/bin/sh
set -e

echo "Installing dependencies ..."
composer install --quiet --no-dev --no-scripts --prefer-dist --no-progress && composer clear-cache --quiet

exec "$@"
