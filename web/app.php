<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$env = 'dev';

if (false !== getenv('SYMFONY_ENV')) {
    $env = getenv('SYMFONY_ENV');
}

$devModes = ['dev', 'test'];

require __DIR__.'/../vendor/autoload.php';

if (PHP_VERSION_ID < 70000) {
    exit('This product should use version >= PHP 7.0');
}

// Load DEV Mode settings
if (in_array($env, $devModes, true)) {
    Debug::enable();
    $kernel = new AppKernel('dev', true);
} else {
    // Load PROD Mode settings
    include_once __DIR__.'/../var/bootstrap.php.cache';
    $kernel = new AppKernel($env, false);
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
