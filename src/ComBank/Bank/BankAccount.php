<?php

namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BankAccountInterface
{
    private $balance;
    private $status;

    public function __construct($balance)
    {
        $this->balance = $balance;
        $this->status=true;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function closeAccount(): void{
        $this->status = false;
    }

    public function reopenAccount(): void{
        $this->status=true;
    }

    public function isOpen(): bool{
        return $this->status;
    }

    public function setBalance(float $balance): void{
        $this->balance = $balance;
    }

    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!$this->isOpen()) {
            throw new BankAccountException("Cannot perform transaction: Account is closed.");
        }

        $newBalance = $transaction->applyTransaction($this);
        $this->setBalance($newBalance);
    }


}
