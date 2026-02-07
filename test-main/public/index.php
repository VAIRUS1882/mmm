<?php
// Turn off all error reporting to prevent any output
error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// Clean ALL output buffers completely
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Start fresh with NO output
ob_start();

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$response = $app->handle(Request::capture());

// Get and discard any output
ob_end_clean();

// Send clean response
$response->send();

$app->terminate();