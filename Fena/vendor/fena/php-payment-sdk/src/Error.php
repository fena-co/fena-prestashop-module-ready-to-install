<?php


namespace Fena\PaymentSDK;


class Error
{
    private $code;
    private $message;

    /**
     * Error constructor.
     * @param $code
     */
    public function __construct($code)
    {
        $this->code = $code[0];
        $this->message = $code[1];
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}