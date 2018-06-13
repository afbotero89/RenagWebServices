<?php

namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use PDO;
use \Slim\Middleware\JwtAuthentication;
use App\v1\src\libs\Customclase;

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

$container = $app->getContainer();

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register folders
$folders = $settings['settings']['folders'];

foreach ($folders as $f) {
	foreach (glob("../src/$f/*.php") as $filename) {
		require $filename;
	}
}

// Run app
$app->run();
?>