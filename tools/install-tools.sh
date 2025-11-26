#!/usr/bin/env bash
set -euo pipefail
mkdir -p tools
cd tools
if [ ! -f phpunit.phar ]; then curl -sSL -o phpunit.phar https://phar.phpunit.de/phpunit-9.6.phar; chmod +x phpunit.phar; fi
if [ ! -f phpDocumentor.phar ]; then curl -sSL -o phpDocumentor.phar https://phpdoc.org/phpDocumentor.phar; chmod +x phpDocumentor.phar; fi
if [ ! -f phpmetrics.phar ]; then curl -sSL -o phpmetrics.phar https://github.com/phpmetrics/PhpMetrics/releases/download/v3.0.2/phpmetrics.phar; chmod +x phpmetrics.phar; fi
echo "Tools ready."
