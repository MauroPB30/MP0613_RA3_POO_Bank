<?php

namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class InterestTransaction extends BaseTransaction implements BankTransactionInterface
{
    // Constructor que no obliga a pasar cantidad inicial porque se calcula con el Overdraft
    public function __construct()
    {
        $this->amount = 0.0;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        // Aca preguntamos a la cuenta que porcentaje de interes tiene su Overdraft (1, 5 o 10)
        $interestRate = $account->getOverdraft()->getInterestRate();

        // Aca calculamos cuanto dinero de interes gana: Saldo * (Porcentaje / 100)
        $this->amount = $account->getBalance() * ($interestRate / 100);

        // Luevo devolvemos el saldo actual + el interés ganado
        return $account->getBalance() + $this->amount;
    }

    public function getTransactionInfo(): string
    {
        return 'INTEREST_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}