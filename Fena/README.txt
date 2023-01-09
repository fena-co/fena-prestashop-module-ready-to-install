Hello there :) 
Prestashop Module Guide Qasim Malik is here!!!!!
Its Pretty Simple to Understand.....
Just keep there points in Mind :) 

1) The Main File is Fena.php it contains Name of the plugin and Type of the Module and contains some hooks.
2) The Front Part is defined in Controllers/Front
3)All the templates are defined in \Fena\views\templates
4) When the user Hits the Place Order Button it is Redirected to Fena\Controllers\Front\Payment.php
5) the Webhook is receieved at Fena\Controller\Front\webhook.php
6)the Redirect page is Fena\Controller\Front\Notification.php.

