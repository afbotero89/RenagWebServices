<?php
require '/Applications/XAMPP/xamppfiles/htdocs/RenagWebServices/stripe-php-6.1.0/init.php';	
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


// Add new user
$app->post('/payments/newCustomer',function(Request $request, Response $response){

    $number = $request->getParam('number');
    $exp_month = $request->getParam('exp_month');
    $exp_year = $request->getParam('exp_year'); 
    $cvc = $request->getParam('cvc');

    \Stripe\Stripe::setApiKey("sk_test_ryR5UOujCJAZJer2zWiAfiyT");

    $token = \Stripe\Token::create(array(
        "card" => array(
          "number" => $number,
          "exp_month" => $exp_month,
          "exp_year" => $exp_year,
          "cvc" => $cvc
        )
      ));

    \Stripe\Stripe::setApiKey("sk_test_ryR5UOujCJAZJer2zWiAfiyT");

    $userObject = \Stripe\Customer::create(array(
        "description" => "Customer for afbotero89@gmail.com",
        "source" => $token, // obtained with Stripe.js
        "email" => "afbotero89@gmail.com"
    ));
    echo $userObject;
});


// Create new charge
$app->post('/payments/charge',function(Request $request, Response $response){

    \Stripe\Stripe::setApiKey("sk_test_ryR5UOujCJAZJer2zWiAfiyT");

    $amount = $request->getParam('amount');
    $description = $request->getParam('description');
    $receipt_email = $request->getParam('receipt_email');
    $number = $request->getParam('number');
    $exp_month = $request->getParam('exp_month');
    $exp_year = $request->getParam('exp_year'); 
    $cvc = $request->getParam('cvc');

    //$customer = $request->getParam('customer');

    $token = \Stripe\Token::create(array(
        "card" => array(
          "number" => $number,
          "exp_month" => $exp_month,
          "exp_year" => $exp_year,
          "cvc" => $cvc
        )
      ));
    //echo $token;

    $chargeObject = \Stripe\Charge::create(array(
		"amount" => $amount,
		"currency" => "aud",
		"source" => $token, // obtained with Stripe.js
		"description" => $description,
		"receipt_email" => $receipt_email,
		//"customer" => $customer
	));
    echo $chargeObject;
    
});

// Payment with user already created
$app->post('/payments/newTransfer',function(Request $request, Response $response){
    \Stripe\Stripe::setApiKey("sk_test_ryR5UOujCJAZJer2zWiAfiyT");

    $amount = $request->getParam('amount');
    $description = $request->getParam('description');
    $receipt_email = $request->getParam('receipt_email');

    $chargeObject = \Stripe\Charge::create(array(
		"amount" => $amount,
		"currency" => "aud",
		"description" => $description,
		"receipt_email" => $receipt_email,
		"customer" => "cus_CQRzgIBR1DPixo"
	));
    echo $chargeObject;
});
