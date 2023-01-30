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
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// this is required to load autoload file.
require_once 'vendor/autoload.php';
// Classes Required
use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\DeliveryAddress;
use Fena\PaymentSDK\Error;
use Fena\PaymentSDK\Payment;
use Fena\PaymentSDK\User;

class FenaPaymentModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        return parent::init();
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function setMedia()
    {
        return parent::setMedia();
    }

    public function postProcess()
    {
        parent::postProcess();
        $cart = $this
            ->context->cart;
        $cartID = $cart->id;
        $integrationId = Configuration::get('FENA_CLIENTID');
        $integrationSecret = Configuration::get('FENA_CLIENTSECRET');
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $amount = (string) $total;
        $orderIdd = $cart->id;
        $orderId = (string) $orderIdd;
        $customer = new Customer($cart->id_customer);
        $billingEmail = $customer->email;
        $firstName = $customer->firstname;
        $lastName = $customer->lastname;
        $customerAdress = $this
            ->context
            ->cart->id_address_delivery;
        $details = $customer->getSimpleAddress($customerAdress);
        $deliveryAdress1 = $details['address1'];
        $deliveryAdress2 = $details['address2'];
        $phNum = $details['phone'];
        $postalCode = $details['postcode'];
        $country = $details['country'];
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

        try {
            $connection = Connection::createConnection($integrationId, $integrationSecret);
            $user = User::createUser($billingEmail, $firstName, $lastName, $phNum);
            if ($user instanceof Error) {
                return [
                    'result' => 'failure',
                    'messages' => 'Something went wrong. Please contact support.',
                ];
            }

            $payment = Payment::createPayment($connection, $amount, $orderId);
            $payment->setUser($user);
            $country = $details['country'];

            $deliveryAddress = DeliveryAddress::createDeliveryAddress($deliveryAdress1, $deliveryAdress2, $postalCode, $details['city'], $country);

            if ($deliveryAddress instanceof DeliveryAddress) {
                $payment->setDeliveryAddress($deliveryAddress);
            }

            $url = $payment->process();
            $hashed = $payment->getHashedId();
            Configuration::updateValue('hashedID', $hashed);
            Tools::redirect($url);
        } catch (Exception $e) {
            echo 'error in connection';
            echo 'Message' . $e->getMessage();
        }
    }
}
