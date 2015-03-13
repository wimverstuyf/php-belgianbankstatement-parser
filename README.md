# php-belgianbankstatement-parser
Unified parser for several bank statement formats from Belgian banks.
Supports CODA, MT940 and CSV (BNP Paribas)

[![Build Status](https://secure.travis-ci.org/wimverstuyf/php-belgianbankstatement-parser.png?branch=master)](https://travis-ci.org/wimverstuyf/php-belgianbankstatement-parser)

## Installation

You can install Codelicious/BelgianBankStatement using Composer. You can read more about Composer and its main repository at
[http://packagist.org](http://packagist.org "Packagist"). First install Composer for your project using the instructions on the
Packagist home page, then define your dependency on Codelicious/BelgianBankStatement in your `composer.json` file.

```json
    {
        "require": {
            "codelicious/php-belgianbankstatement-parser": "dev-master"
        }
    }
```

## Usage

```php
<?php

use Codelicious\BelgianBankStatement\Parsers;

$parser = new Parser();
$statement = $parser->parseFile('coda-file.cod', 'coda');

echo $statement->date . "\n";

foreach ($statement->transactions as $transaction) {
    echo $transaction->account->name . ": " . $transaction->amount . "\n";
}

echo $statement->new_balance . "\n";
```
    
## Statement structure

*   `Codelicious\BelgianBankStatement\Statement`
    *   `date` Date of the supplied file (format YYYY-MM-DD)
    *   `account` Account for which the statements were created. An object implementing `Codelicious\BelgianBankStatement\Account`
    *   `original_balance` Balance of the account before the transactions were processed. Up to 3 decimals.
    *   `new_balance` Balance of the account after the transactions were processed. Up to 3 decimals.
    *   `transaction` A list of transactions implemented as `Codelicious\BelgianBankStatement\Transaction`
*   `Codelicious\BelgianBankStatement\Account`
    *   `name` Name of the holder of the account
    *   `bic` Bankcode of the account
    *   `number` Banknumber of the account
    *   `currency` Currency of the account
    *   `country` Country of the account
*   `Codelicious\BelgianBankStatement\Transaction`
    *   `account` Account of the other party of the transaction. An object implementing `Codelicious\BelgianBankStatement\Account`
    *   `transaction_date` Date on which the transaction was requested
    *   `valuta_date` Date on which the transaction was executed by the bank
    *   `amount` Amount of the transaction. Up to 3 decimals. A negative number for credit transactions.
    *   `message` Message of the transaction
    *   `structured_message` Structured messages of the transaction (if available)

