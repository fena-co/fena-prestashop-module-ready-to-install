<?php
/**
* Copyright since 2023 Fena Labs Ltd
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author   "Fena <support@fena.co>"
*  @copyright Since 2023 Fena Labs Ltd
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
//these lines are to enable debug mode and it will display errors here.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//this is required to load autoload file.
require_once ('vendor/autoload.php');

use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Payment;
use Fena\PaymentSDK\Error;
class FenaWebhookModuleFrontController extends ModuleFrontController{

public function init()
{
    return parent::init();
    //Here we check whether the user is signed in or not

}
public function initContent()
{
    parent::initContent();
    //here we assign the user InterFace
}
public function setMedia()
{
    return parent::setMedia();
}
public function postProcess(){

parent::postProcess();
//get the data coming with the redirect
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['status'])) {
    die();
}
if (!isset($data['reference'])) {
    die();
}
//extract these three values from incomming stream store in a variable
$orderId = $data['reference'];
$status = $data['status'];
$amount = $data['amount'];



//In Order to Test the incomming values We need to verify it from Fena By help of SDK

  ///////////////////////--->Start<---//////////////////////////////////
//get hased ID you saved in previous page
$hashedId = Configuration::get('hashedID');
$terminal_id = Configuration::get('FENA_CLIENTID');
$terminal_secret = Configuration::get('FENA_CLIENTSECRET');
//get All values we saved on payment page for webhook
$cartId = Configuration::get('fenaCartId');
$totalAmount = Configuration::get('fenaTotal');
$currencyId = Configuration::get('fenaCurrenyId');
$customerKey = Configuration::get('fenaCustomerKey');
$ModuleDisplayName = Configuration::get('fenaModuleName');

//create connection to fena
$connection = Connection::createConnection(
    $terminal_id,
    $terminal_secret
);

if ($connection instanceof Error) {
    return array(
        'result' => 'failure',
        'messages' => 'Something went wrong. Please contact support.'
    );
}
//create payment again 
$payment = Payment::createPayment(
    $connection,
    $amount,
    $orderId
);
//pass the hashed ID save previously pass it to SDK's function 
$serverData = $payment->checkStatusByHashedId($hashedId);
  //this is the response from the SDK
  $dat = json_encode($serverData,true);
  echo $dat;
  //get Fena TransactionID
  $FenaTransactionId=$serverData['data']['transaction'];

if ($serverData['data']['status'] != $status) {
    $status = $serverData['data']['status'];
}
//if Status is paid Validate the order with payment status paid
if($status=='paid'){
    $this->module->validateOrder($cartId ,
    Configuration::get('PS_OS_WS_PAYMENT'),
    $totalAmount,
    $ModuleDisplayName,
    null,
    array('transaction_id'=>$FenaTransactionId),
    $currencyId,
    false,
    $customerKey
);

//this will be used on redirect side in Notification.php. 
$conformationLink='Order_Confirmed';
Configuration::updateValue('LinkConfirmation',$conformationLink);


}
//if the payment status is rejected 
else if($status=='rejected'){
//this will be used on Redirect Page in Notification.php
$link2= 'order_rejected';
Configuration::updateValue('LinkConfirmation',$link2);
}
//exit is to close the page once Webhook gave all values to Prestashop 
exit();
    
//////////////////////--->End<---/////////////////////////////////////
     
 }


}