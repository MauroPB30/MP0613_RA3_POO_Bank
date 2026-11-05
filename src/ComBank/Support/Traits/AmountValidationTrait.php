<?php

namespace ComBank\Support\Traits;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 2:35 PM
 */
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;

trait AmountValidationTrait
{
    /**
     * @param float $amount
     * @throws InvalidArgsException
     * @throws ZeroAmountException
     */
    public function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new ZeroAmountException("Transaction amount must be greater than zero.");
        }
    }

    public function validateEmail (string $email): void{
        // filter_var devuelve false si el email NO tiene formato válido (ej: "johnexample.com")
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BankAccountException("Invalid email format"); 

        }
    }
}
