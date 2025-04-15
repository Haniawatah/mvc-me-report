<?php

// Show all errors to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Kernel;

$projectDir = dirname(__DIR__);

// Try to load the autoloader
if (file_exists($projectDir.'/vendor/autoload_runtime.php')) {
    require_once $projectDir.'/vendor/autoload_runtime.php';
} elseif (file_exists($projectDir.'/vendor/autoload.php')) {
    require_once $projectDir.'/vendor/autoload.php';
} else {
    echo '<h1>Autoloader Not Found</h1>';
    echo '<p>Could not find autoloader. Please run composer install in the project root directory:</p>';
    echo '<code>cd ' . htmlspecialchars($projectDir) . ' && composer install</code>';
    exit;
}

try {
    return function (array $context) {
        return new Kernel($context['APP_ENV'] ?? 'dev', (bool) ($context['APP_DEBUG'] ?? true));
    };
} catch (Throwable $e) {
    echo '<h1>Error</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    exit;
}
?>
