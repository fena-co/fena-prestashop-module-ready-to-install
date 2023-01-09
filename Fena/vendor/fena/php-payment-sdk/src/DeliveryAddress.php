<?php

namespace Fena\PaymentSDK;

class DeliveryAddress
{
    /**
     * @var string
     */
    private $addressLine1;

    /**
     * @var string
     */
    private $addressLine2;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $country;

    /**
     * @param string $addressLine1
     * @param string $addressLine2
     * @param string $postCode
     * @param string $city
     * @param string $country
     * @return DeliveryAddress|Error
     */
    public static function createDeliveryAddress(string $addressLine1,
                                      string $addressLine2,
                                      string $postCode,
                                      string $city,
                                      string $country = 'UK'
    )
    {
        // cannot be empty
        if($addressLine1 == ''){
            return new Error(Errors::CODE_23);
        }

        // validate greater than 255
        if (strlen($addressLine1) > 255) {
            return new Error(Errors::CODE_23);
        }

        // validate greater than 255
        if (strlen($addressLine2) > 255) {
            return new Error(Errors::CODE_24);
        }

        // cannot be empty
        if($postCode == ''){
            return new Error(Errors::CODE_25);
        }

        // validate greater than 255
        if (strlen($postCode) > 255) {
            return new Error(Errors::CODE_25);
        }

        // cannot be empty
        if($city == ''){
            return new Error(Errors::CODE_26);
        }

        // validate greater than 255
        if (strlen($city) > 255) {
            return new Error(Errors::CODE_26);
        }


        // must be 2 digits
        if (strlen($country) != 2) {
            return new Error(Errors::CODE_27);
        }

        return new DeliveryAddress($addressLine1, $addressLine2, $postCode, $city, $country);
    }

    /**
     * DeliveryAddress constructor.
     * @param string $addressLine1
     * @param string $addressLine2
     * @param string $postCode
     * @param string $city
     * @param string $country
     */
    private function __construct(string $addressLine1, string $addressLine2, string $postCode, string $city, string $country)
    {
        $this->addressLine1 = $addressLine1;
        $this->addressLine2 = $addressLine2;
        $this->zipCode = $postCode;
        $this->city = $city;
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    /**
     * @return string
     */
    public function getAddressLine2(): string
    {
        return $this->addressLine2;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->zipCode;
    }


    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
}