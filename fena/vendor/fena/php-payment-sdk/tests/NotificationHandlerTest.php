<?php


use FaizPay\PaymentSDK\Connection;
use FaizPay\PaymentSDK\Errors;
use FaizPay\PaymentSDK\NotificationHandler;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;
use FaizPay\PaymentSDK\Error;

class NotificationHandlerTest extends TestCase
{

    private $id = '1536bc14-9273-460e-9711-7e96733616fe';
    private $secret = 'f9fd95d5-2acb-4643-b3fb-b16999f37175';

    private function getValidConnection()
    {
        return Connection::createConnection($this->id, $this->secret);
    }

    public function testErrorOnInvalidToken()
    {
        $connection = $this->getValidConnection();
        $payment = NotificationHandler::createNotificationHandler($connection, 'asd');
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_16[1]);
    }

    public function testErrorOnInvalidSecret()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10',
                'netAmount' => '10',
                'terminal' => '1536bc14-9273-460e-9711-7e96733616fe',
            ],
            'asd',
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_16[1]);
    }

    public function testErrorOnExpireToken()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'exp' => time() - 100,
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10',
                'netAmount' => '10',
                'terminal' => '1536bc14-9273-460e-9711-7e96733616fe',
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_16[1]);
    }

    public function testErrorOnInvalidTokenContent()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'netAmount' => '10',
                'terminal' => '1536bc14-9273-460e-9711-7e96733616fe',
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_17[1]);
    }

    public function testErrorOnTerminalMisMatch()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10',
                'netAmount' => '10',
                'terminal' => $this->secret,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertInstanceOf(Error::class, $payment);
        $this->assertEquals($payment->getMessage(), Errors::CODE_18[1]);
    }


    public function testCreateTerminalOnValidData()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10.00',
                'netAmount' => '10.00',
                'terminal' => $this->id,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertInstanceOf(NotificationHandler::class, $payment);
    }


    public function testValidateAmountCheck()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10.00',
                'netAmount' => '10.00',
                'terminal' => $this->id,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertTrue($payment->validateAmount(10.00));
        $this->assertTrue($payment->validateAmount("10.00"));
        $this->assertTrue($payment->validateAmount("10.00000000000000"));
        $this->assertTrue($payment->validateAmount("10"));
        $this->assertFalse($payment->validateAmount("11.00000001"));
        $this->assertFalse($payment->validateAmount("ABC"));
        $this->assertFalse($payment->validateAmount("A$$!@£!@£!@3BC"));
    }

    public function testGetRequestedAmount()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10.00',
                'netAmount' => '10.00',
                'terminal' => $this->id,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertEquals('10.00', $payment->getRequestedAmount());
    }

    public function testGetTerminal()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10.00',
                'netAmount' => '10.00',
                'terminal' => $this->id,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertEquals($this->id, $payment->getTerminal());
    }

    public function testGetOrderID()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10.00',
                'netAmount' => '10.00',
                'terminal' => $this->id,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertEquals('abc', $payment->getOrderID());
    }

    public function testGetNetAmount()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10.00',
                'netAmount' => '11.00',
                'terminal' => $this->id,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertEquals('11.00', $payment->getNetAmount());
    }

    public function testGetId()
    {
        $connection = $this->getValidConnection();
        $token = JWT::encode(
            [
                'id' => '1',
                'orderID' => 'abc',
                'requestAmount' => '10.00',
                'netAmount' => '11.00',
                'terminal' => $this->id,
            ],
            $this->secret,
            'HS512'
        );
        $payment = NotificationHandler::createNotificationHandler($connection, $token);
        $this->assertEquals('1', $payment->getId());
    }
}
