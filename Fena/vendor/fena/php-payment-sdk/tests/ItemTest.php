<?php


use FaizPay\PaymentSDK\Errors;
use FaizPay\PaymentSDK\Item;
use FaizPay\PaymentSDK\User;
use PHPUnit\Framework\TestCase;
use \FaizPay\PaymentSDK\Error;

class ItemTest extends TestCase
{

    public function testErrorOnNameMoreThan255Characters()
    {
        $item = Item::createItem(str_repeat('a', 256), 1);
        $this->assertInstanceOf(Error::class, $item);
        $this->assertEquals($item->getMessage(), Errors::CODE_19[1]);
    }


    public function testErrorOnQuantityLessThan1()
    {
        $item = Item::createItem('Cup', 0);
        $this->assertInstanceOf(Error::class, $item);
        $this->assertEquals($item->getMessage(), Errors::CODE_20[1]);

        $item = Item::createItem('Cup', -10);
        $this->assertInstanceOf(Error::class, $item);
        $this->assertEquals($item->getMessage(), Errors::CODE_20[1]);
    }

    public function testGetName()
    {
        $item = Item::createItem('Cup', 1);
        $this->assertEquals('Cup', $item->getName());
    }

    public function testGetQuantity()
    {
        $item = Item::createItem('Cup', 1);
        $this->assertEquals(1, $item->getQuantity());
    }


    public function testGetToArray()
    {
        $item = Item::createItem('Cup', 1);
        $this->assertEquals(
            [
                'name' => 'Cup',
                'quantity' => 1
            ], $item->toArray());
    }
}
