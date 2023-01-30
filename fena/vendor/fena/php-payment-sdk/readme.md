Fena PHP Payment SDK
=======
SDK for working with Fena payment APIs.


Documentation
------------
Full documentation can be found at: https://docs.fena.co


Requirements
------------
PHP 7.0.0 and later.

Installation
------------

You can install the bindings via Composer. Run the following command:

```bash
composer require fena/php-payment-sdk
```

To use the bindings, use Composer's autoload:

```php
require_once('vendor/autoload.php');
```

Dependencies
------------
-   [`json`](https://secure.php.net/manual/en/book.json.php)
-   [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)


Getting Started
------------
Simple new payment looks like:

```php
use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Payment;

$connection = Connection::createConnection(
    $integrationId = '8afa74ae9fe8be53db50',
    $integrationSecret = '55d7d5ed-be22-4321-bb3f-aec8524d8be2'
);

$payment = Payment::createPayment(
    $connection,
    $amount = '10.00'
    $reference = 'AA-11', 
);
 
$payment->process();
```

__Optional: Set User or Pre Selected Provider For New Payment__

```php
use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Payment;
use Fena\PaymentSDK\Provider;
use Fena\PaymentSDK\User;

$connection = Connection::createConnection($terminalId, $terminalSecret);
$payment = Payment::createPayment(
    $connection,
    $amount = '10.00',
    $reference = 'AA-11', 
);

$user = User::createUser(
    $email = 'john.doe@test.com',
    $firstName = 'John',
    $lastName = 'Doe',
    $contactNumber = '07000845953'
);
$payment->setUser($user);

$provider = Provider::createProvider(
    $providerId = 'lloyds-bank',
    $sortCode = '123456',
    $accountNumber = '12345678'
);
$payment->setProvider($provider);
```