<?php
	require '/Applications/XAMPP/xamppfiles/htdocs/RenagWebServices/stripe-php-6.1.0/init.php';	
	echo 'payments';

	//echo $data;

	// Create a charge

	\Stripe\Stripe::setApiKey("sk_test_ryR5UOujCJAZJer2zWiAfiyT");

/*
	$chargeObject = \Stripe\Charge::create(array(
		"amount" => 50,
		"currency" => "aud",
		//"source" => "tok_visa", // obtained with Stripe.js
		"description" => "test 24 february for usert test 1",
		"receipt_email" => "felipe.botero.ospina@gmail.com",
		"customer" => "cus_CL8WuCkrwAhOOn"
	));
	echo $chargeObject;
*/

	$token = \Stripe\Token::create(array(
        "card" => array(
          "number" => "4242424242424242",
          "exp_month" => 1,
          "exp_year" => 2020,
          "cvc" => "314"
        )
      ));
   
	$userObject = \Stripe\Customer::create(array(
		
        "description" => "Customer for camilo@example.com",
        "source" => $token, // obtained with Stripe.js
        "email" => "camilo@example.com",


        //"source" => $data;
    ));
    echo $userObject;

	
	//\Stripe\Charge::retrieve("ch_1BvxRPGf9JPLG1Dv241c8OJ1", array('api_key' => "sk_test_ryR5UOujCJAZJer2zWiAfiyT"))

?>