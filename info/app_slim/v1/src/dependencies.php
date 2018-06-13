<?php

use Monolog\Logger;
use App\v1\src\libs\fpdf\PDF;
use App\v1\src\libs\Validation;
use App\v1\src\libs\AttachMailer;
// DIC configuration

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// pdo
$container['db'] = function ($c){
	$settings = $c->get('settings')['db'];
	$pdo = new PDO("mysql:host=" . $settings['host'] . ";dbname=" . $settings['dbname'], $settings['user'], $settings['pass']);
    $pdo->exec("set names utf8");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $pdo;
};

$container['pdf'] = function($c){
    $settings = $c->get('settings')['asthmapp'];
    // Create folders to pdf files
    if(!file_exists($settings['files_path'])){
        mkdir($settings['files_path'], 0777, true);
    }
    // generate pdf file
    $pdf = new PDF('P','pt','A4');
    return $pdf;
};

$container['validator'] = function($c){
    $validator = new Validation();

    return $validator;
};

$container['pwd_generator'] = function($c){
    $secret = $c->get('settings')['security']['pwd'];
    $pwd = password_hash($secret, PASSWORD_DEFAULT);
    return substr($pwd, 8, 8);
};

$container['mailSender'] = function($c){
    $mailSender = new AttachMailer();
    return $mailSender;
}
?>
