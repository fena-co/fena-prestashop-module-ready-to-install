<?php


use FaizPay\PaymentSDK\Error;
use FaizPay\PaymentSDK\Errors;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{


    public function testGetCode()
    {
        $error = new Error(Errors::CODE_1);
        $this->assertEquals(Errors::CODE_1[0], $error->getCode());
    }

    public function testGetMessage()
    {
        $error = new Error(Errors::CODE_1);
        $this->assertEquals(Errors::CODE_1[1], $error->getMessage());
    }
}
