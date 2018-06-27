#!/bin/bash

console="bin/console"

echo "Updating database..."
php $console doctrine:cache:clear-metadata
php $console doctrine:cache:clear-query
php $console doctrine:schema:update --force
