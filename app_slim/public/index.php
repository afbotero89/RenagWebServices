<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;
$app->get('/user/{service}', function (Request $request, Response $response) {
    $service = $request->getAttribute('service');
    $response->getBody()->write("User, $service");

    return $response;
});

require '../src/queries/dbmanagement.php';
require '../src/queries/stripePayments.php';
require '../src/queries/stolenBikes.php';

$app->run();
