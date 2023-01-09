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
require_once ('vendor/autoload.php');

use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Error;
use Fena\PaymentSDK\Payment;

class FenaNotificationModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        return parent::init();

    }
    public function initContent()
    {
        parent::initContent();
        //here we assign the user InterFace
        
    }

    public function postProcess()
    {
        parent::postProcess();
        //just get the refrence and Status from link
        $refrence = $_GET['order_id'];
        $status = $_GET['status'];
        //get the link we stored in previous page in a variable e.g (Order_Cofirmed/Rejected)
        $linke = Configuration::get('LinkConfirmation');

        //impliment Simple Checks to the link....
        if ($linke == 'Order_Confirmed')
        {
            //get All values we stored previously they are required to create Order Conformation Page.
            $cartId = Configuration::get('fenaCartId');
            $totalAmount = Configuration::get('fenaTotal');
            $languageId = Configuration::get('languageId');
            $currencyId = Configuration::get('fenaCurrenyId');
            $customerKey = Configuration::get('fenaCustomerKey');
            $ModuleDisplayName = Configuration::get('fenaModuleName');

            //this is how order conformation page is made
            $conformationLink = $this
                ->context
                ->link
                ->getPageLink('order-confirmation', Configuration::get('PS_SSL_ENABLED') , $languageId, 'id_cart=' . $cartId . '&id_module=' . $this
                ->module->id . '&id_order=' . $this
                ->module->currentOrder . '&key=' . $customerKey);
            //redirect user to order Conformation
            Tools::redirect($conformationLink);

        }
        //if order is rejected redirect user to Order Page with Empty cart.
        else if ($linke == 'order_rejected')
        {
            Tools::redirect($this
                ->context
                ->link
                ->getPageLink('order'));
        }

    }

}

