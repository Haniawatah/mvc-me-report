#!/bin/bash
# Script to install tools in tools/ directory

for tool in php-cs-fixer phpmd phpstan phpmetrics; do
    echo "Installing $tool..."
    if [ -d "tools/$tool" ]; then
        (cd "tools/$tool" && composer install)
    else
        echo "Directory tools/$tool does not exist."
    fi
done
