#!/bin/bash

# Find all PHP files and convert CRLF to LF
find src tests -name "*.php" -type f -exec sed -i 's/\r$//' {} \;

# Find all Twig template files and convert CRLF to LF
find templates -name "*.twig" -type f -exec sed -i 's/\r$//' {} \;

# Find all YAML files and convert CRLF to LF
find config -name "*.yaml" -type f -exec sed -i 's/\r$//' {} \;

# Fix .htaccess file
sed -i 's/\r$//' .htaccess

# Fix XML files
find . -name "*.xml" -type f -exec sed -i 's/\r$//' {} \;

echo "Line endings fixed for PHP, Twig, YAML, and XML files"
