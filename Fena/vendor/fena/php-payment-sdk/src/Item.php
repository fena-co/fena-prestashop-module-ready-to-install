<?php

namespace Fena\PaymentSDK;

class Item
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param string $name
     * @param int $quantity
     * @return Error|Item
     */
    public static function createItem(string $name,
                                      int $quantity
    )
    {
        $name = trim($name);
        // validate greater than 255
        if (strlen($name) > 255) {
            return new Error(Errors::CODE_19);
        }

        // if quantity is less than  1
        $quantity = trim($quantity);
        if ($quantity < 1) {
            return new Error(Errors::CODE_20);
        }
        return new Item($name, $quantity);
    }

    /**
     * Item constructor.
     * @param string $name
     * @param int $quantity
     */
    private function __construct(string $name, int $quantity)
    {
        $this->name = $name;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'quantity' => $this->getQuantity(),
        ];
    }

}