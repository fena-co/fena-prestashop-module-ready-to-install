<?php


use FaizPay\PaymentSDK\Errors;
use FaizPay\PaymentSDK\Provider;
use PHPUnit\Framework\TestCase;
use \FaizPay\PaymentSDK\Error;

class ProviderTest extends TestCase
{

    public function testErrorOnInvalidSortCode()
    {
        $provider = Provider::createProvider('ob-lloyds', '12345', '12345678');
        $this->assertInstanceOf(Error::class, $provider);
        $this->assertEquals($provider->getMessage(), Errors::CODE_7[1]);
    }

    public function testErrorOnInvalidAccountNumber()
    {
        $provider = Provider::createProvider('ob-lloyds', '123456', '1234567899');
        $this->assertInstanceOf(Error::class, $provider);
        $this->assertEquals($provider->getMessage(), Errors::CODE_8[1]);
    }

    public function testErrorOnEmptyProviderButSortCodeAndAccountGiven()
    {
        $provider = Provider::createProvider('', '123456', '12345678');
        $this->assertInstanceOf(Error::class, $provider);
        $this->assertEquals($provider->getMessage(), Errors::CODE_9[1]);
    }

    public function testErrorOnSortCodeOrAccountNumberIsMissing()
    {
        $provider = Provider::createProvider('ob-lloyds', '', '12345678');
        $this->assertInstanceOf(Error::class, $provider);
        $this->assertEquals($provider->getMessage(), Errors::CODE_10[1]);

        $provider = Provider::createProvider('ob-lloyds', '123456', '');
        $this->assertInstanceOf(Error::class, $provider);
        $this->assertEquals($provider->getMessage(), Errors::CODE_10[1]);
    }

    public function createNewProviderWithJustProvider()
    {
        $provider = Provider::createProvider('ob-lloyds');
        $this->assertInstanceOf(Provider::class, $provider);
    }

    public function createNewProviderWithProviderAndAccountDetails()
    {
        $provider = Provider::createProvider('ob-lloyds','123456','12345678');
        $this->assertInstanceOf(Provider::class, $provider);
    }

    public function testGetAccountNumber()
    {
        $provider = Provider::createProvider('ob-lloyds','123456','12345678');
        $this->assertEquals('12345678', $provider->getAccountNumber());
    }

    public function testGetProviderId()
    {
        $provider = Provider::createProvider('ob-lloyds','123456','12345678');
        $this->assertEquals('ob-lloyds', $provider->getProviderId());
    }

    public function testGetSortCode()
    {
        $provider = Provider::createProvider('ob-lloyds','123456','12345678');
        $this->assertEquals('123456', $provider->getSortCode());
    }
}
