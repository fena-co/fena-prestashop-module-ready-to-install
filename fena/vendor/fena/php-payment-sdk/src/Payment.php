<?php

namespace Fena\PaymentSDK;

use Fena\PaymentSDK\Helper\NumberFormatter;

class Payment
{
    private $endpoint = 'https://business.api.fena.co/public/payments/create-and-process';
    private $checkEndpoint = 'https://business.api.fena.co/payment-flow/public/payment/';

    protected $refNumber;
    protected $orderId;
    protected $amount;
    protected $user;
    protected $provider;
    protected $items = [];
    protected $deliveryAddress;
    protected $reference = null;
    protected $hashedId;
    protected $customRedirectURL = null;


    /**
     * @param Connection $connection
     * @param string $orderId client order id
     * @param string $amount amount in 2 decimal places
     * @param string|null $customRedirectUrl custom redirect URL for the current payment
     * @return Error|Payment
     */
    public static function createPayment(
        Connection  $connection,
        string      $amount,
        string      $reference,
        ?string     $customRedirectURL = null
    )
    {
        // validate amount
        if ($amount == '' || $amount == '0.00' || (float)$amount < 0) {
            return new Error(Errors::CODE_4);
        }

// validate amount
        if (!NumberFormatter::validateTwoDecimals($amount)) {
            return new Error(Errors::CODE_5);
        }

// validate order id is greater than 18
        if (strlen($reference) > 12) {
            return new Error(Errors::CODE_6);
        }

        return new Payment($connection, $reference, $amount, $customRedirectURL);
    }

    /**
     * Payment constructor.
     * @param Connection $connection
     * @param $orderId string unique order id
     * @param $amount  string amount requested
     * @param ?string $customRedirectURL custom redirect URL for the current payment
     */
    private function __construct(
        Connection  $connection,
        string      $orderId,
        string      $amount,
        ?string     $customRedirectURL = null
    )
    {
        $this->connection = $connection;
        $this->refNumber = $orderId;
        $this->amount = $amount;
        if (!is_null($customRedirectURL)) {
            $this->customRedirectURL = $customRedirectURL;
        }
    }


    /**
     * Set the optional user for payment
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): Payment
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set the optional provider for payment
     * @param Provider|null $provider
     * @return $this
     */
    public function setProvider(?Provider $provider): Payment
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * add a new item to a order
     * @param Item $item
     * @return $this
     */
    public function addItem(Item $item): Payment
    {
        $this->items[] = $item->toArray();
        return $this;
    }

    /**
     * Set the optional delivery Address for payment
     * @param DeliveryAddress|null $deliveryAddress
     * @return $this
     */
    public function setDeliveryAddress(?DeliveryAddress $deliveryAddress): Payment
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * process the payment
     * @return Error|string
     */
    public function process()
    {
        $curl = curl_init();
        $payload = [
            'invoiceRefNumber' => $this->refNumber,
            'amount' => $this->amount,
            'customerEmail' => '',
            'customerName' => '',
            'items' => $this->items,
        ];

        if (!is_null($this->customRedirectURL)) {
            $payload['customRedirectUrl'] = $this->customRedirectURL;
        }

        if ($this->user instanceof User) {
            $payload['customerEmail'] = (string)$this->user->getEmail();
            $payload['customerName'] = $this->user->getFirstName() . ' ' . $this->user->getLastName();
        }

        if ($this->deliveryAddress instanceof DeliveryAddress) {
            $payload['deliveryAddress'] =
                [
                    'addressLine1' => (string)$this->deliveryAddress->getAddressLine1(),
                    'addressLine2' => (string)$this->deliveryAddress->getAddressLine2(),
                    'zipCode' => (string)$this->deliveryAddress->getPostCode(),
                    'city' => (string)$this->deliveryAddress->getCity(),
                    'country' => (string)$this->deliveryAddress->getCountry()
                ];
        }

        $integrationId = $this->connection->getIntegrationId();
        $integrationSecret = $this->connection->getIntegrationSecret();
        $headers = array('Content-Type: application/json', "secret-key: {$integrationSecret}", "integration-id: {$integrationId}");

        curl_setopt($curl, CURLOPT_URL, $this->endpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($curl);

        if ($e = curl_error($curl)) {
            return new Error(Errors::CODE_22);
        } else {

            // Decoding JSON data
            $decodedData =
                json_decode($response, true);


            if ($decodedData['created'] === true) {
                $link = $decodedData['result']['link'];
                $pos = strpos($link, "=");
                $this->hashedId = substr($link, $pos + 1);
                return $decodedData['result']['link'];
            } else {
                return new Error(Errors::CODE_22);
            }
        }
    }

    public function manuallyCheckStatus()
    {
        $curl = curl_init();
        $headers = array('Content-Type: application/json');
        curl_setopt($curl, CURLOPT_URL, $this->checkEndpoint . $this->hashedId);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if ($e = curl_error($curl)) {
            return new Error(Errors::CODE_22);
        } else {

            // Decoding JSON data
            $decodedData =
                json_decode($response, true);


            if ($decodedData) {
                return $decodedData;
            } else {
                return new Error(Errors::CODE_22);
            }
        }
    }

    public function checkStatusByHashedId(string $hashed)
    {
        $curl = curl_init();
        $headers = array('Content-Type: application/json');
        curl_setopt($curl, CURLOPT_URL, $this->checkEndpoint . $hashed);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if ($e = curl_error($curl)) {
            return new Error(Errors::CODE_22);
        } else {

            // Decoding JSON data
            $decodedData =
                json_decode($response, true);


            if ($decodedData) {
                return $decodedData;
            } else {
                return new Error(Errors::CODE_22);
            }
        }
    }

    public function getHashedId()
    {
        return $this->hashedId;
    }
}
