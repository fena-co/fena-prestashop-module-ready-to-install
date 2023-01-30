<?php


namespace Fena\PaymentSDK;


class Provider
{
    /**
     * @var string | null
     */
    private $providerId;

    /**
     * @var string | null
     */
    private $sortCode;

    /**
     * @var string | null
     */
    private $accountNumber;

    /**
     * @param string $providerId
     * @param string|null $sortCode
     * @param string|null $accountNumber
     * @return Error|Provider
     */
    public static function createProvider(
        string $providerId,
        ?string $sortCode = null,
        ?string $accountNumber = null
    )
    {
        $providerId = trim($providerId);

        $sortCode = trim($sortCode);
        if ($sortCode != '' && strlen(trim($sortCode)) != '6') {
            return new Error(Errors::CODE_7);
        }

        $accountNumber = trim($accountNumber);
        if ($accountNumber != '' && strlen(trim($accountNumber)) != '8') {
            return new Error(Errors::CODE_8);
        }


        if ($providerId == '' && ($sortCode != '' || $accountNumber != '')) {
            return new Error(Errors::CODE_9);
        }

        if ($sortCode != '' && $accountNumber == '') {
            return new Error(Errors::CODE_10);
        }

        if ($accountNumber != '' && $sortCode == '') {
            return new Error(Errors::CODE_10);
        }

        return new Provider($providerId, $sortCode, $accountNumber);
    }

    /**
     * Provider constructor.
     * @param string|null $providerId
     * @param string|null $sortCode
     * @param string|null $accountNumber
     */
    private function __construct(?string $providerId, ?string $sortCode, ?string $accountNumber)
    {
        $this->providerId = $providerId;
        $this->sortCode = $sortCode;
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return string|null
     */
    public function getProviderId(): ?string
    {
        return $this->providerId;
    }


    /**
     * @return string|null
     */
    public function getSortCode(): ?string
    {
        return $this->sortCode;
    }

    /**
     * @return string|null
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

}