<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\GoldOverdraft;
use ComBank\Transactions\InterestTransaction;

require_once 'bootstrap.php';


//---[Bank account 1]---/
// create a new account1 with balance 400
$bankAccount1 = new BankAccount(400);

pl('--------- [Start testing bank account #1, No overdraft] --------');
try {
    // show balance account
    pl('with current balance ' . $bankAccount1->getBalance());

    // close account
    $bankAccount1->closeAccount();
    // reopen account
    $bankAccount1->reopenAccount();

    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150));
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());

    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25));
    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600));
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());




//---[Bank account 2]---/

$bankAccount2 = new BankAccount(200); // o el saldo inicial que indique el ejercicio
$bankAccount2->applyOverdraft(new SilverOverdraft());

pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    // show balance account

    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100));
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    // withdrawal -300
    pl('Doing transaction deposit (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction deposit (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120));
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
} catch (\Exception $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
} catch (\Exception $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());

try {
} catch (BankAccountException $e) {
    pl($e->getMessage());
}

//---[Bank account 3]---/
echo "<h3>--- Testing Email Validation (Issue #2) ---</h3>";

// Sample 1: Email válido
try {
    $bankAccount3 = new BankAccount(500.0, "john.smith@example.com");
    pl("Sample 1:");
    pl("Email: " . $bankAccount3->getEmail());
    pl("Result: Account created successfully.");
} catch (BankAccountException $e) {
    pl("Result: Account creation rejected due to an " . strtolower($e->getMessage()) . ".");
}

pl(""); 
// Sample 2: Intentar crear bankAccount3 con email inválido
try {
    pl("Sample 2:");
    pl("Email: johnexample.com");
    $bankAccount3Invalid = new BankAccount(500.0, "johnexample.com");
    pl("Result: Account created successfully.");
} catch (BankAccountException $e) {
    pl("Result: Account creation rejected due to an " . strtolower($e->getMessage()) . ".");
}
pl(""); 

echo "<h3>--------- [Start testing Interest Transaction - Issue #3] --------</h3>";
// Sample 1: NoOverdraft (1% interest)
$accountSample1 = new BankAccount(1000.0);
pl("Sample 1:");
pl("Account Balance: $" . number_format($accountSample1->getBalance(), 2));
pl("Overdraft Strategy: NoOverdraft");
pl("Interest Rate: " . $accountSample1->getOverdraft()->getInterestRate() . "%");
$accountSample1->transaction(new InterestTransaction());
pl("Balance After Transaction: $" . number_format($accountSample1->getBalance(), 2));

pl("");

// Sample 2: SilverOverdraft (5% interest)
$accountSample2 = new BankAccount(1000.0);
$accountSample2->applyOverdraft(new SilverOverdraft());
pl("Sample 2:");
pl("Account Balance: $" . number_format($accountSample2->getBalance(), 2));
pl("Overdraft Strategy: SilverOverdraft");
pl("Interest Rate: " . $accountSample2->getOverdraft()->getInterestRate() . "%");
$accountSample2->transaction(new InterestTransaction());
pl("Balance After Transaction: $" . number_format($accountSample2->getBalance(), 2));

pl("");

// Sample 3: GoldOverdraft (10% interest)
$accountSample3 = new BankAccount(1000.0);
$accountSample3->applyOverdraft(new GoldOverdraft());
pl("Sample 3:");
pl("Account Balance: $" . number_format($accountSample3->getBalance(), 2));
pl("Overdraft Strategy: GoldOverdraft");
pl("Interest Rate: " . $accountSample3->getOverdraft()->getInterestRate() . "%");
$accountSample3->transaction(new InterestTransaction());
pl("Balance After Transaction: $" . number_format($accountSample3->getBalance(), 2));