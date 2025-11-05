<?php

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
