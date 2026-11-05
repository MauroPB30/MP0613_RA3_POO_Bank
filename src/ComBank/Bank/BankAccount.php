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
    use AmountValidationTrait;
    private $balance;
    private $status;
    private $overdraft;
    private string $email;

    public function __construct($balance = 0.0, string $email = "default@example.com")
    {
        $this->validateEmail($email);

        $this->balance = $balance;
        $this->status = true;
        $this->overdraft = new NoOverdraft();
        $this->email = $email;
    }
    public function getBalance(): float
    {
        return $this->balance;
    }
    public function closeAccount(): void
    {
        $this->status = false;
    }
    public function reopenAccount(): void
    {
        if ($this->isOpen()) {
            throw new BankAccountException("Cannot reopen: Account is already open.");
        }
        $this->status = true;
    }
    public function isOpen(): bool
    {
        return $this->status;
    }
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!$this->isOpen()) {
            throw new BankAccountException("Cannot perform transaction: Account is closed.");
        }
        try{
        $newBalance = $transaction->applyTransaction($this);
        $this->setBalance($newBalance);
    } catch (\ComBank\Exceptions\InvalidOverdraftFundsException $e) {
            throw new \ComBank\Exceptions\FailedTransactionException($e->getMessage());
        }
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }
    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->validateEmail($email); // Validamos también por si intentan cambiarlo después por uno inválido
        $this->email = $email;
    }
}

