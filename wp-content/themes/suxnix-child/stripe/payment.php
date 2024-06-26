
<?php 
  ini_set('error_reporting', E_ALL);
 ini_set('display_errors', true);
 require_once('vendor/autoload.php');

    // global $wpdb;
    // echo $txnref;
    
    // //check that txn_id has not been previously processed
    // $old_txn = $wpdb->get_results("SELECT code FROM thnw_pmpro_membership_orders WHERE user_id = '" . $txnref . "' ");
    // $orderid = $old_txn[0]->code;
    // echo $orderid;
 

    
    
      //stripe secret key or revoke key
      $stripeSecret = 'sk_test_51JPl9FHZFjMDtIDT5Km0PweujXNpbBfM2iv3MgB9Xsum2IBnWzQTeUssGkHMa1Aa5WI0CGZM9fEStafacV8uC1Uk00KmCVkXMa';

        // $stripe = new \Stripe\StripeClient('sk_test_51JPl9FHZFjMDtIDT5Km0PweujXNpbBfM2iv3MgB9Xsum2IBnWzQTeUssGkHMa1Aa5WI0CGZM9fEStafacV8uC1Uk00KmCVkXMa');
    \Stripe\Stripe::setApiKey('sk_test_51JPl9FHZFjMDtIDT5Km0PweujXNpbBfM2iv3MgB9Xsum2IBnWzQTeUssGkHMa1Aa5WI0CGZM9fEStafacV8uC1Uk00KmCVkXMa');
   
   
   
 /*  // Create a Charge:
$charge = \Stripe\Charge::create(array(
  "amount" => 100,
  "currency" => "USD",
  "source" => "tok_visa",
  "transfer_group" => "ORDER_95",
));*/

// Create a Transfer to a connected account (later):
$transfer = \Stripe\Transfer::create(array(
  "amount" => 70,
  "currency" => "USD",
  "destination" => "acct_1JQShWQn9z77mFAB",
  "transfer_group" => "ORDER_95",
));

// Create a second Transfer to another connected account (later):
$transfer = \Stripe\Transfer::create(array(
  "amount" => 20,
  "currency" => "USD",
  "destination" => "acct_1JQTKjQlkHinnOUV",
  "transfer_group" => "ORDER_95",
));

die('test');
/*
    ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
    //die('test');
    
     $transfer = $stripe->transfers->create([
       'amount' => 400,
       'currency' => 'usd',
       'destination' => '4111111111111111',
       'transfer_group' => '54A1494B8D',
     ]);
    print_r($transfer);
    die('test');
        
       */
//       echo 'hoy';
//       $stripe = new \Stripe\StripeClient('sk_test_51JPl9FHZFjMDtIDT5Km0PweujXNpbBfM2iv3MgB9Xsum2IBnWzQTeUssGkHMa1Aa5WI0CGZM9fEStafacV8uC1Uk00KmCVkXMa');
//       print_r('$stripe');
// $acc = $stripe->accounts->create([
//   'type' => 'custom',
//   'country' => 'US',
//   'email' => 'piyushmathur10@gmail.com',
//   'capabilities' => [
//     'card_payments' => ['requested' => true],
//     'transfers' => ['requested' => true],
//   ],
// ]);

  try{
       $account = \Stripe\Account::create([
  'country' => 'US',
  'type' => 'custom',
  'email' => 'pacebytecom@gmail.com',
 // 'status' => 'new',
   'business_type'=>'individual',
//   "routing_number"=> "110000000",
//   "industry" => "",
//   'Address' => 'address_full_check',
//   'dob' => '1901-01-01',
//   'representative' => 'piyush',
  'capabilities' => [
    'card_payments' => [
      'requested' => true,
    ],
    'transfers' => [
      'requested' => true,
   
    ],
    
  ],
  //'tos_acceptance[service_agreement]'=>'recipient',
]);
$paymentIntent = \Stripe\PaymentIntent::create([
  'amount' => 10000,
  'currency' => 'USD',
  /*'transfer_data' => [
    'destination' => 'acct_1JPl9FHZFjMDtIDT',
  ],*/ 
  
  'payment_method_types' => ['card'],
  'transfer_group' => 'ORDER_95',
]);
$transfer = \Stripe\Transfer::create([
  "amount" => 70,
  "currency" => "usd",
  "destination" => "acct_1JPl9FHZFjMDtIDT",
  "transfer_group" => "ORDER_95",
  
]);
print_r($paymentIntent);
//die('test');
}

 catch (\Stripe\Exception\RateLimitException $e) {
      echo $e->getError();
    } catch (\Stripe\Exception\InvalidRequestException $e) {
      echo $e->getError();
    } catch (\Stripe\Exception\AuthenticationException $e) {
      echo $e->getError();
    } catch (\Stripe\Exception\ApiConnectionException $e) {
      echo $e->getError();
    } catch (\Stripe\Exception\ApiErrorException $e) {
      echo $e->getError();
    } catch (Exception $e) {
      echo $e;
    }
         /*
echo 'hello';
   print_r($account);
   die('test');
      
// Create a PaymentIntent:
$paymentIntent = \Stripe\PaymentIntent::create([
  'amount' => 10000,
  'currency' => 'inr',
  'payment_method_types' => ['card'],
  'transfer_group' => '{ORDER10}',
]);
//print_r($paymentIntent);
//die('test');
// Create a Transfer to a connected account (later):
$transfer = \Stripe\Transfer::create([
  'amount' => 7000,
  'currency' => 'inr',
  //'destination' => '{{dsgahlot92@gmail.com}}',
  //'transfer_group' => '{ORDER10}',
]);
print_r($transfer);
die('test');

// Create a second Transfer to another connected account (later):
$transfer = \Stripe\Transfer::create([
  'amount' => 2000,
  'currency' => 'usd',
  'destination' => '{{OTHER_CONNECTED_STRIPE_ACCOUNT_ID}}',
  'transfer_group' => '{ORDER10}',
]);
*/
?>
