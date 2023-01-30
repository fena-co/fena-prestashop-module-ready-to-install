<?php

use FaizPay\PaymentSDK\Connection;
use FaizPay\PaymentSDK\Error;
use FaizPay\PaymentSDK\Errors;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{

    public function testInvalidIDForCreateConnection()
    {
        $id = 'aa';
        $secret = 'f9fd95d5-2acb-4643-b3fb-b16999f37175';
        $connection = Connection::createConnection($id, $secret);
        $this->assertInstanceOf(Error::class, $connection);
        $this->assertEquals($connection->getMessage(), Errors::CODE_1[1]);
    }

    public function testInvalidSecretForCreateConnection()
    {
        $id = 'f9fd95d5-2acb-4643-b3fb-b16999f37175';
        $secret = 'aa';
        $connection = Connection::createConnection($id, $secret);
        $this->assertInstanceOf(Error::class, $connection);
        $this->assertEquals($connection->getMessage(), Errors::CODE_2[1]);
    }

    public function testInvalidSecretAndIdForCreateConnection()
    {
        $id = 'aa';
        $secret = 'aa';
        $connection = Connection::createConnection($id, $secret);
        $this->assertInstanceOf(Error::class, $connection);
        $this->assertEquals($connection->getMessage(), Errors::CODE_1[1]);
    }

    public function testCreateConnectionForValidIDAndSecret()
    {
        $id = '1536bc14-9273-460e-9711-7e96733616fe';
        $secret = 'f9fd95d5-2acb-4643-b3fb-b16999f37175';
        $connection = Connection::createConnection($id, $secret);
        $this->assertInstanceOf(Connection::class, $connection);
    }

    public function testGetTerminalSecret()
    {
        $id = '1536bc14-9273-460e-9711-7e96733616fe';
        $secret = 'f9fd95d5-2acb-4643-b3fb-b16999f37175';
        $connection = Connection::createConnection($id, $secret);
        $this->assertEquals($connection->getTerminalSecret(), $secret);
    }

    public function testGetTerminalId()
    {
        $id = '1536bc14-9273-460e-9711-7e96733616fe';
        $secret = 'f9fd95d5-2acb-4643-b3fb-b16999f37175';
        $connection = Connection::createConnection($id, $secret);
        $this->assertEquals($connection->getTerminalId(), $id);
    }
}
