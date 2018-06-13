<?php
//require_once('vendor/autoload.php');
require '/Applications/XAMPP/xamppfiles/htdocs/RenagWebServices/stripe-php-6.1.0/init.php';	
$stripe = array(
  "secret_key"      => "sk_test_ryR5UOujCJAZJer2zWiAfiyT",
  "publishable_key" => "pk_test_04hMm2WPChgXgDd0PUQaQvMq"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>
