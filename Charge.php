<?php
require_once('stripe/init.php');
\Stripe\Stripe::setApiKey("sk_live_8DejeFn0loY5NLukOhP3uieP");

// Token is created using Checkout or Elements!
// Get the payment token ID submitted by the form:
$token = $_POST['stripeToken'];

// Charge the user's card:
$charge = \Stripe\Charge::create(array(
    "amount" => 1000000,
    "currency" => "eur",
    "description" => "order-10000",
    "source" => $token
    ));
echo "支付成功";
