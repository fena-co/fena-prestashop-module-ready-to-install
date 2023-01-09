<?php


use FaizPay\PaymentSDK\Connection;
use FaizPay\PaymentSDK\Errors;
use FaizPay\PaymentSDK\Payment;
use FaizPay\PaymentSDK\Provider;
use FaizPay\PaymentSDK\User;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;
use \FaizPay\PaymentSDK\Error;

class PaymentTest extends TestCase
{
    private $id = '1536bc14-9273-460e-9711-7e96733616fe';
    private $secret = 'f9fd95d5-2acb-4643-b3fb-b16999f37175';

    private function getValidConnection()
    {
        return Connection::createConnection($this->id, $this->secret);
    }

    /**
     * @param string $order
     * @param string $amount
     * @param User|null $user
     * @param Provider|null $provider
     * @return object
     */
    private function getToken($order = 'abc', $amount = '1.00', ?User $user = null, ?Provider $provider = null): object
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, $order, $amount);
        $payment->setProvider($provider);
        $payment->setUser($user);
        $url = $payment->process(false);
        $token = explode('=', $url)[1];
        return JWT::decode($token, $connection->getTerminalSecret(), ['HS512']);
    }

    public function testErrorOnEmptyOrderId()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, '', '10.00');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_3[1]);
    }

    public function testErrorOnSpaceTabOrderId()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, ' ', '10.00');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_3[1]);

        $payment = Payment::createPayment($connection, '    ', '10.00');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_3[1]);
    }

    public function testErrorOnOrderIdGreaterThan255()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, str_repeat('a', 256), '10.00');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_6[1]);
    }

    public function testErrorOnZeroAmount()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, 'abc', '0.00');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_4[1]);

    }

    public function testErrorOnEmptyAmount()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, 'abc', '');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_4[1]);
    }

    public function testErrorOnLessThanZeroAmount()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, 'abc', '-1.00');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_4[1]);
    }

    public function testErrorOnMoreOrLessThan2DecimalPlacesForAmount()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, 'abc', '0.00000000001');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_5[1]);

        $payment = Payment::createPayment($connection, 'abc', '1');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_5[1]);
    }

    public function testErrorOnValidAmount()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, 'abc', 'A.BB');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_5[1]);
    }

    public function testCreatePaymentClassOnValidInput()
    {
        $connection = $this->getValidConnection();
        $payment = Payment::createPayment($connection, 'abc', '1.00');
        $this->assertInstanceOf(Payment::class, $payment);
    }

    public function testTokenHasRequiredPropertiesAndEncodedWithCorrectKey()
    {
        $tokenData = $this->getToken();
        $this->assertObjectHasAttribute('iat', $tokenData);
        $this->assertObjectHasAttribute('exp', $tokenData);
        $this->assertObjectHasAttribute('terminalID', $tokenData);
        $this->assertObjectHasAttribute('orderID', $tokenData);
        $this->assertObjectHasAttribute('amount', $tokenData);
    }

    public function testTokenHasCorrectValueEncoded()
    {
        $tokenData = $this->getToken('abc', '100.00');
        $this->assertEquals($this->id, $tokenData->terminalID);
        $this->assertEquals('abc', $tokenData->orderID);
        $this->assertEquals('100.00', $tokenData->amount);
    }

    public function testTokenHasCorrectValueForExtendedUsageEncoded()
    {
        $provider = Provider::createProvider('ob-lloyds', '123456', '12345678');
        $user = User::createUser('test@test.com', 'John', 'Smith', '07000000');

        $tokenData = $this->getToken('abc', '100.00', $user, $provider);
        $this->assertEquals($this->id, $tokenData->terminalID);
        $this->assertEquals('abc', $tokenData->orderID);
        $this->assertEquals('100.00', $tokenData->amount);

        // test the provider
        $this->assertEquals('ob-lloyds', $tokenData->bankID);
        $this->assertEquals('123456', $tokenData->sortCode);
        $this->assertEquals('12345678', $tokenData->accountNumber);

        // test the user
        $this->assertEquals('test@test.com', $tokenData->email);
        $this->assertEquals('John', $tokenData->firstName);
        $this->assertEquals('Smith', $tokenData->lastName);
        $this->assertEquals('07000000', $tokenData->contactNumber);
    }
}
