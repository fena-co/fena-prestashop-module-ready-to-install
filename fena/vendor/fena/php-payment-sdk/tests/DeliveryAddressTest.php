<?php


use FaizPay\PaymentSDK\DeliveryAddress;
use FaizPay\PaymentSDK\Errors;
use FaizPay\PaymentSDK\User;
use PHPUnit\Framework\TestCase;
use \FaizPay\PaymentSDK\Error;

class DeliveryAddressTest extends TestCase
{

    public function testErrorOnCreateFieldMoreThan255CharactersAndNotEmpty()
    {

        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            str_repeat('a', 256),
            'Line 2',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_23[1]);


        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            '',
            'Line 2',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_23[1]);

        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            str_repeat('a', 256),
            'Post Code',
            'City',
            'UK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_24[1]);


        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            str_repeat('a', 256),
            'City',
            'UK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_25[1]);


        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            '',
            'City',
            'UK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_25[1]);

        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            str_repeat('a', 256),
            'UK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_26[1]);

        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            '',
            'UK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_26[1]);
    }

    public function testEmptyAddressLine2(){
          $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            '',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertEquals('', $deliveryAddress->getAddressLine2());
}

    public function testErrorOnCountry(){
        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            'UKK'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_27[1]);

        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            ''
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_27[1]);

        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            'U'
        );
        $this->assertInstanceOf(Error::class, $deliveryAddress);
        $this->assertEquals($deliveryAddress->getMessage(), Errors::CODE_27[1]);
    }


    public function testGetAddressLine1(){
        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertEquals('Line 1', $deliveryAddress->getAddressLine1());
    }

    public function testGetAddressLine2(){
        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertEquals('Line 2', $deliveryAddress->getAddressLine2());
    }

    public function testGetCity(){
        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertEquals('City', $deliveryAddress->getCity());
    }

    public function testGetPostCode(){
        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertEquals('Post Code', $deliveryAddress->getPostCode());
    }

    public function testGetCountry(){
        $deliveryAddress = DeliveryAddress::createDeliveryAddress(
            'Line 1',
            'Line 2',
            'Post Code',
            'City',
            'UK'
        );
        $this->assertEquals('UK', $deliveryAddress->getCountry());
    }

}
