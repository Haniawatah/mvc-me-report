#!/bin/bash

# Backup composer.json first
echo "Creating backup of composer.json..."
cp composer.json composer.json.backup

# Remove lock file and vendor directory
echo "Removing composer.lock and vendor directory..."
rm -f composer.lock
rm -rf vendor

# Install core Symfony 6.2 components
echo "Installing Symfony 6.2 components and Doctrine..."
composer require symfony/console:6.2.* symfony/framework-bundle:6.2.* \
  symfony/yaml:6.2.* symfony/runtime:6.2.* symfony/twig-bundle:6.2.* \
  symfony/dotenv:6.2.* symfony/web-profiler-bundle:6.2.* symfony/config:6.2.* \
  symfony/browser-kit:6.2.* symfony/css-selector:6.2.* symfony/phpunit-bridge:6.2.* \
  symfony/stopwatch:6.2.* doctrine/orm:^2.14 doctrine/doctrine-bundle:^2.8 \
  doctrine/doctrine-migrations-bundle:^3.2 symfony/maker-bundle:^1.48 -W

# Install QA tools (dev)
echo "Installing QA tools (dev)..."
composer require --dev phpmd/phpmd:^2.15 phpstan/phpstan:^1.12 phpmetrics/phpmetrics:^3.0 -W

# Clear Symfony cache
echo "Clearing cache..."
php bin/console cache:clear

# Check Symfony version
echo "Verifying Symfony version..."
php bin/console --version

# Create database config directory if it doesn't exist
mkdir -p config/packages

echo "Done. Your Symfony dependencies should now be fixed."
