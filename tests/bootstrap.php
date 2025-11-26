<?php
declare(strict_types=1);
$composer = __DIR__ . '/../vendor/autoload.php';
if (is_file($composer)) { require $composer; } else {
    spl_autoload_register(function($class){
        $prefix = 'App\\Proj\\';
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
        $rel = substr($class, strlen($prefix));
        $file = __DIR__ . '/../src/Proj/' . str_replace('\\','/',$rel) . '.php';
        if (is_file($file)) require $file;
    });
}
require dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Map test namespace JokerCard to the real implementation to satisfy instanceof
if (!class_exists('App\\Tests\\Card\\JokerCard') && class_exists(\App\Card\JokerCard::class)) {
    class_alias(\App\Card\JokerCard::class, 'App\\Tests\\Card\\JokerCard');
}
