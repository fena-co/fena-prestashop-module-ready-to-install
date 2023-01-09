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
//Classes Required
use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Payment;
use Fena\PaymentSDK\Error;
use Fena\PaymentSDK\User;
use Fena\PaymentSDK\DeliveryAddress;
class FenaPaymentModuleFrontController extends ModuleFrontController
{

    public function init()
    {
        return parent::init();
        //Here we check whether the user is signed in or not
        if (!$this
            ->module->active || !$this
            ->context
            ->cart->id_address_delivery || !$this
            ->context
            ->cart
            ->id_address_invoice)
        {
            Tools::redirect($this
                ->context
                ->link
                ->getPageLink('order'));
        }
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
    public function postProcess()
    {

        parent::postProcess();
        //this is the SDK integeration part
        //get Cart Details
        $cart = $this
            ->context->cart;
        $cartID = $cart->id;
        //get Terminal Id And Secret From Data Base
        $integrationId = Configuration::get('FENA_CLIENTID');
        $integrationSecret = Configuration::get('FENA_CLIENTSECRET');
        //amount will be stored in total
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        //Conversion of Amount to String As Fena SDK requirement
        $amount = strval($total);
        //getting Cart ID As refrence to Pass to SDK
        $orderIdd = $cart->id;
        $orderId = strval($orderIdd);

        ////////////////---->START<----////////////////////////////////
        //Data Required to Create User And Delivery Address
        $customer = new Customer($cart->id_customer);
        $billingEmail = $customer->email;
        $firstName = $customer->firstname;
        $lastName = $customer->lastname;
        $customerAdress = $this
            ->context
            ->cart->id_address_delivery;
        //some Details lie in this Function getSimpleAdress As an array in Customer.php class of Prestashop
        $details = $customer->getSimpleAddress($customerAdress);
        $deliveryAdress1 = $details['address1'];
        $deliveryAdress2 = $details['address2'];
        $phNum = $details['phone'];
        $postalCode = $details['postcode'];
        $country = $details['country'];
        ///////////////////-->END<---//////////////////
        ///////////////////////--->Start<----/////////////////////////////////
        /////....Storing Some Values to Database As webhook page cant get these values from Context...
        $languageID = $this
            ->context
            ->language->id;
        $customerKey = $customer->secure_key;
        $currencyID = $this
            ->context
            ->currency->id;
        $moduleName = $this
            ->module->displayName;
        Configuration::updateValue('fenaCartId', $cartID);
        Configuration::updateValue('fenaCurrenyId', $currencyID);
        Configuration::updateValue('fenaCustomerKey', $customerKey);
        Configuration::updateValue('fenaModuleName', $moduleName);
        Configuration::updateValue('languageId', $languageID);
        Configuration::updateValue('fenaTotal', $total);
        /////////--->END<---/////////////////////
        

        //implimented try catch cz it throws unknwn errors
        try
        {
            //Create Connection For SDK
            $connection = Connection::createConnection($integrationId, $integrationSecret);
            //Create User from the values got Above
            $user = User::createUser($billingEmail, $firstName, $lastName, $phNum);
            if ($user instanceof Error)
            {
                return array(
                    'result' => 'failure',
                    'messages' => 'Something went wrong. Please contact support.'
                );
            }
            //create the payment
            $payment = Payment::createPayment($connection, $amount, $orderId);
            //Set the User First
            $payment->setUser($user);

            ////////////////////-->Start<---////////////////////////////////
            // add delivery address
            $country = $details['country'];

            $deliveryAddress = DeliveryAddress::createDeliveryAddress($deliveryAdress1, $deliveryAdress2, $postalCode, $details['city'], $country);

            if ($deliveryAddress instanceof DeliveryAddress)
            {
                $payment->setDeliveryAddress($deliveryAddress);
            }
            //////////////////////---->END<----//////////////////////////////
            //process the Payment
            $url = $payment->process();
            //get hashedId from Fena SDK
            $hashed = $payment->getHashedId();
            //Store it in the dataBase
            Configuration::updateValue('hashedID', $hashed);
            //redirect to the URL created by SDK
            Tools::redirect($url);

        }
        catch(Exception $e)
        {
            echo 'error in connection';
            echo 'Message' . $e->getMessage();
        }

    }

}

