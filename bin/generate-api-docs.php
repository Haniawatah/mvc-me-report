#!/usr/bin/env php
<?php

require dirname(__DIR__).'/vendor/autoload.php';

use OpenApi\Generator;

// Make sure the docs/api directory exists
$docsDir = dirname(__DIR__).'/public/docs/api';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0777, true);
}

// Generate OpenAPI documentation
$openapi = Generator::scan([dirname(__DIR__).'/src/Controller']);

// Save OpenAPI documentation as JSON
file_put_contents($docsDir.'/openapi.json', $openapi->toJson());
echo "OpenAPI JSON documentation generated at: {$docsDir}/openapi.json\n";

// Also save as YAML for developers
file_put_contents($docsDir.'/openapi.yaml', $openapi->toYaml());
echo "OpenAPI YAML documentation generated at: {$docsDir}/openapi.yaml\n";

// Generate a basic README
$readmeContent = <<<MD
# API Documentation

This directory contains the API documentation for the MVC course project.

## Endpoints

The API provides several endpoints for interacting with the application:

- `/api` - Interactive API documentation
- `/api/example` - Example API endpoint
- `/api/game` - Game state information

## OpenAPI/Swagger

This documentation was generated using OpenAPI/Swagger. The schema is available in both JSON and YAML formats:

- [OpenAPI JSON](openapi.json)
- [OpenAPI YAML](openapi.yaml)

To view the interactive documentation, visit the `/api` route in your browser.
MD;

file_put_contents($docsDir.'/README.md', $readmeContent);
echo "README documentation generated at: {$docsDir}/README.md\n";

// Create a copy in the docs/api directory
$projectDocsDir = dirname(__DIR__).'/docs/api';
if (!is_dir($projectDocsDir)) {
    mkdir($projectDocsDir, 0777, true);
}

copy($docsDir.'/openapi.json', $projectDocsDir.'/openapi.json');
copy($docsDir.'/openapi.yaml', $projectDocsDir.'/openapi.yaml');
copy($docsDir.'/README.md', $projectDocsDir.'/README.md');

echo "\nAPI documentation successfully generated and copied to both:\n";
echo "- public/docs/api/ (web accessible)\n";
echo "- docs/api/ (project documentation)\n";
