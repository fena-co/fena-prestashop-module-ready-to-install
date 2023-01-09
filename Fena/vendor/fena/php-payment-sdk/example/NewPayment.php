<?php


use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Payment;
use Fena\PaymentSDK\Provider;
use Fena\PaymentSDK\User;

class NewPayment
{

    public function createNewPayment()
    {
        $terminalId = '8afa74ae93b29fe8be53db50';
        $terminalSecret = '55d7d5ed-be22-4321-bb3f-aec8524d8be2';
        $orderId = 'ABC';
        $amount = '10.00';

        $connection = Connection::createConnection($terminalId, $terminalSecret);

        $payment = Payment::createPayment(
            $connection,
            $amount,
            $orderId
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

        $url = $payment->process();
    }


}