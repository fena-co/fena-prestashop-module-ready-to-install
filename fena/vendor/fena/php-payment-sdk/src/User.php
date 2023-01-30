<?php

namespace Fena\PaymentSDK;


class User
{
    /**
     * @var string | null
     */
    private $email;

    /**
     * @var string | null
     */
    private $firstName;

    /**
     * @var string | null
     */
    private $lastName;

    /**
     * @var string | null
     */
    private $contactNumber;


    /**
     * @param string|null $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $contactNumber
     * @return Error|User
     */
    public static function createUser(?string $email,
                                      ?string $firstName = null,
                                      ?string $lastName = null,
                                      ?string $contactNumber = null
    )
    {
        $email = trim($email);
        if ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Error(Errors::CODE_11);
        }

        // validate greater than 255
        if (strlen($email) > 255) {
            return new Error(Errors::CODE_15);
        }

        $firstName = trim($firstName);
        // validate greater than 255
        if (strlen($firstName) > 255) {
            return new Error(Errors::CODE_12);
        }

        $lastName = trim($lastName);
        // validate greater than 255
        if (strlen($lastName) > 255) {
            return new Error(Errors::CODE_13);
        }

        $contactNumber = trim($contactNumber);
        // validate greater than 255
        if (strlen($contactNumber) > 255) {
            return new Error(Errors::CODE_14);
        }

        return new User($email, $firstName, $lastName, $contactNumber);
    }

    /**
     * User constructor.
     * @param string|null $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $contactNumber
     */
    private function __construct(?string $email, ?string $firstName, ?string $lastName, ?string $contactNumber)
    {


        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->contactNumber = $contactNumber;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }
}