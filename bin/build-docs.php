#!/usr/bin/env php
<?php

require dirname(__DIR__).'/vendor/autoload.php';

use OpenApi\Generator;

$openapi = Generator::scan([dirname(__DIR__).'/src/Controller']);

$docsDir = dirname(__DIR__).'/public';
file_put_contents($docsDir.'/openapi.json', $openapi->toJson());

echo "OpenAPI documentation generated successfully.\n";
