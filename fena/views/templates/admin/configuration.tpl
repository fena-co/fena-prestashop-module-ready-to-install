{*
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
*  @author    "Fena <support@fena.co>"
*  @copyright Since 2023 Fena Labs Ltd
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<form method="post">
    <div class="config-Top">
        <img src="" class="logo1">
        
    </div>
<div class="panel">
    <div class="panel-heading">
        {l s='Configuration' mod='fena'}

    </div>
    <div class="panel-body">

          <h1>Please Enter Client ID and Secret</h1>

            <label for="print">{l s='Enter ClientId' mod='fena'}</label>
            <input type="text"
                   name="clientId"
                   id="clientId"
                   class="form-control"
                   placeholder="8afa74ae-6ef9-48bb-93b2-9fe8be53db50"
                   value="{$FENA_CLIENTID}"/>

        <label for="print">{l s='Enter Your Client Secret' mod='fena'}</label>
        <input type="text"
               name="clientSecret"
               id="clientSecret"
               class="form-control"
               placeholder="8afa74ae-6ef9-48bb-93b2-9fe8be53db50"
               value="{$FENA_CLIENTSECRET}"/>

    </div>
    <div class="panel-footer">
        <button type="submit" class="btn-default pull-right" name="fenaClient">
            <i class="process-icon-save"></i>
            {l s='Save' mod='fena'}
        </button>

    </div>
        <div>
    <h3 style="padding-left: 20px"> Webhook & Redirect: </h3>
    <p>Webhook URL: https://{$Webhook}/module/fena/webhook</p>
    <p>Redirect URL: https://{$Webhook}/module/fena/Notification</p>




    </div>

</div>
</form>