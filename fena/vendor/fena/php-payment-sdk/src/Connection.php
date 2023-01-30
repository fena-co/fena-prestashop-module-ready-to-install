<?php

namespace Fena\PaymentSDK;

final class Connection
{
    /**
     * @var string
     */
    protected $connectionType;

    /**
     * @var string
     */
    protected $integrationId;

    /**
     * @var string
     */
    protected $integrationSecret;

    /**
     * @param $integrationId string Integration ID from Fena Business portal (for merchants) or partner API key ID (for partner type)
     * @param $integrationSecret string terminal secret from faizpay portal
     * @param $connectionType string Connection type ('merchant' or 'partner')
     * @return Error|Connection
     */
    public static function createConnection(string $integrationId, string $integrationSecret, string $connectionType = 'merchant')
    {
        if (!self::validate($integrationId)) {
            return new Error(Errors::CODE_1);
        }

        if (!self::validate($integrationSecret)) {
            return new Error(Errors::CODE_2);
        }

        if (!self::validate($connectionType)) {
            return new Error(Errors::CODE_21);
        }
        return new Connection($integrationId, $integrationSecret, $connectionType);
    }

    /**
     * @param $uuid
     * @return bool
     */
    private static function validate($uuid): bool
    {
        if (!is_string($uuid)) {
            return false;
        }
        return true;
    }


    /**
     * Connection constructor.
     * @param string $integrationId
     * @param string $integrationSecret
     * @param string $connectionType
     */
    private function __construct(string $integrationId, string $integrationSecret, string $connectionType)
    {
        $this->integrationId = $integrationId;
        $this->integrationSecret = $integrationSecret;
        $this->connectionType = $connectionType;
    }


    /**
     * @return string
     */
    public function getIntegrationSecret(): string
    {
        return $this->integrationSecret;
    }

    /**
     * @return string
     */
    public function getIntegrationId(): string
    {
        return $this->integrationId;
    }

    /**
     * @return string
     */
    public function getConnectionType(): string
    {
        return $this->connectionType;
    }
}