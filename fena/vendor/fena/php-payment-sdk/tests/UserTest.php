<?php


use FaizPay\PaymentSDK\Errors;
use FaizPay\PaymentSDK\User;
use PHPUnit\Framework\TestCase;
use \FaizPay\PaymentSDK\Error;

class UserTest extends TestCase
{

    public function testErrorOnCreateUserWithInvalidEmail()
    {
        $user = User::createUser('test@test1com', 'John', 'Smith', '07000000');
        $this->assertInstanceOf(Error::class, $user);
        $this->assertEquals($user->getMessage(), Errors::CODE_11[1]);
    }


    public function testErrorOnCreateFieldMoreThan255Characters()
    {
        $user = User::createUser('test@test.com', str_repeat('a', 256), 'Smith', '07000000');
        $this->assertInstanceOf(Error::class, $user);
        $this->assertEquals($user->getMessage(), Errors::CODE_12[1]);

        $user = User::createUser('test@test.com', 'john', str_repeat('a', 256), '07000000');
        $this->assertInstanceOf(Error::class, $user);
        $this->assertEquals($user->getMessage(), Errors::CODE_13[1]);

        $user = User::createUser('test@test.com', 'john', 'smith', str_repeat('a', 256));
        $this->assertInstanceOf(Error::class, $user);
        $this->assertEquals($user->getMessage(), Errors::CODE_14[1]);
    }

    public function testCreateUserOnValidInput(){
        $user = User::createUser('test@test.com', 'john', 'smith', '07497123123');
        $this->assertInstanceOf(User::class, $user);
    }


    public function testGetFirstName()
    {
        $user = User::createUser('test@test.com', 'john', 'smith', '07497123123');
        $this->assertEquals('john', $user->getFirstName());
    }

    public function testGetEmail()
    {
        $user = User::createUser('test@test.com', 'john', 'smith', '07497123123');
        $this->assertEquals('test@test.com', $user->getEmail());
    }

    public function testGetLastName()
    {
        $user = User::createUser('test@test.com', 'john', 'smith', '07497123123');
        $this->assertEquals('smith', $user->getLastName());
    }

    public function testGetContactNumber()
    {
        $user = User::createUser('test@test.com', 'john', 'smith', '07497123123');
        $this->assertEquals('07497123123', $user->getContactNumber());
    }
}
