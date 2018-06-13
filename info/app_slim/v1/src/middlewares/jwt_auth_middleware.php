<?php

use \Slim\Middleware\JwtAuthentication;
use App\v1\src\libs\Response;
use App\v1\src\models as Models;

//middleware for login
$app->add(new JwtAuthentication([
    "path" => "/pressure/login",
    "secret" => $settings['settings']['jwt']['secret'],
    "header" => "X-Token",
    "regexp" => "/(.*)/",
    "algorithm" => "HS256",
    "logger" => $container->get('logger'),
    
    "callback" => function ($req, $res, $args) use ($app) {
        //create a container instance
        $container = $app->getContainer();
        //get data
        $decoded = $args['decoded'];
        $data['email'] = $decoded->email;
        $data['pass'] = $decoded->pass;
        $data['profile'] = $decoded->profile;

        //Write Log
        $container->logger->addInfo("Request login for " . $decoded->email);
        //Make user model
        $model = new Models\Pressure_admin_model($container->db);
        //Set response in container
        $container['login'] = $model->validateUser($data);
    },
    "error" => function($req, $res, $args) use ($app){
        //create new response
        $response = new Response();
        $response->setResponse(false, $args['message']);
        return $res
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
    ])
);

?>