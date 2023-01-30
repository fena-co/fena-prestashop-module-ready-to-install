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

use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Error;
use Fena\PaymentSDK\Payment;

class FenaWebhookModuleFrontController extends ModuleFrontController
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
        $data = json_decode(Tools::file_get_contents('php://input'), true);
        if (!isset($data['status'])) {
            exit;
        }
        if (!isset($data['reference'])) {
            exit;
        }
        $orderId = $data['reference'];
        $status = $data['status'];
        $amount = $data['amount'];
        $hashedId = Configuration::get('hashedID');
        $terminal_id = Configuration::get('FENA_CLIENTID');
        $terminal_secret = Configuration::get('FENA_CLIENTSECRET');
        $cartId = Configuration::get('fenaCartId');
        $totalAmount = Configuration::get('fenaTotal');
        $currencyId = Configuration::get('fenaCurrenyId');
        $customerKey = Configuration::get('fenaCustomerKey');
        $ModuleDisplayName = Configuration::get('fenaModuleName');
        $connection = Connection::createConnection(
            $terminal_id,
            $terminal_secret
        );

        if ($connection instanceof Error) {
            return [
                'result' => 'failure',
                'messages' => 'Something went wrong. Please contact support.',
            ];
        }

        $payment = Payment::createPayment(
            $connection,
            $amount,
            $orderId
        );

        $serverData = $payment->checkStatusByHashedId($hashedId);
        $dat = json_encode($serverData, true);
        $FenaTransactionId = $serverData['data']['transaction'];

        if ($serverData['data']['status'] != $status) {
            $status = $serverData['data']['status'];
        }
        if ($status == 'paid') {
            $this->module->validateOrder(
                $cartId,
                Configuration::get('PS_OS_WS_PAYMENT'),
                $totalAmount,
                $ModuleDisplayName,
                null,
                ['transaction_id' => $FenaTransactionId],
                $currencyId,
                false,
                $customerKey
            );
            $conformationLink = 'Order_Confirmed';
            Configuration::updateValue('LinkConfirmation', $conformationLink);
        } elseif ($status == 'rejected') {
            $link2 = 'order_rejected';
            Configuration::updateValue('LinkConfirmation', $link2);
        }

        exit;
    }
}
