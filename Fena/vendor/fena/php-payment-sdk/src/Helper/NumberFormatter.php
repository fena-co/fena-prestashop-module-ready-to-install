<?php

namespace Fena\PaymentSDK\Helper;

class NumberFormatter
{
    /**
     * @param $number
     * @return string
     */
    public static function formatNumber($number): string
    {
        $number = (string)$number;
        return number_format($number, "2", ".", "");
    }

    /**
     * @param $number
     * @return bool
     */
    public static function validateTwoDecimals($number): bool
    {
        if (preg_match('/^[0-9]+\.[0-9]{2}$/', $number))
            return true;
        else
            return false;
    }

}