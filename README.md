# php-belgianbankstatement-parser

Unified parser for several bank statement formats from Belgian banks.
Supports CODA, MT940 and CSV (BNP Paribas / Belfius / KBC / ING / Crelan / Triodos)

## Installation

You can install Codelicious/BelgianBankStatement using Composer. You can read more about Composer and its main repository at
[http://packagist.org](http://packagist.org "Packagist"). First install Composer for your project using the instructions on the
Packagist home page, then define your dependency on Codelicious/BelgianBankStatement in your `composer.json` file.

```json
    {
        "require": {
            "codelicious/php-belgianbankstatement-parser": "^2.0"
        }
    }
```

Or you can execute the following command in your project root to install this library:

```sh
composer require codelicious/php-belgianbankstatement-parser
```

## Usage

```php
<?php

use Codelicious\BelgianBankStatement;

$parser = new Parser();
$statement = $parser->parseFile('coda-file.cod', 'coda');

echo $statement->getDate()->format('Y-m-d') . "\n";

foreach ($statement->getTransactions() as $transaction) {
    echo $transaction->getAccount()->getName() . ": " . $transaction->getAmount() . "\n";
}

echo $statement->newBalance() . "\n";
```

## Encoding considerations

It's possible that non UTF-8 characters are present in the statements.
In order to unify the encoding of your ingested statements, you might want to consider converting the encoding to UTF-8 before parsing.

```php
if (mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1']) === 'ISO-8859-1') {
  // use mb_convert_encoding($content, to_encoding, from_encoding) to convert to UTF-8
  $content = mb_convert_encoding($content, "UTF-8", "ISO-8859-1");
}
```

## Statement structure

* `Codelicious\BelgianBankStatement\Statement`
  * `Date` - Date of the supplied file (format YYYY-MM-DD)
  * `Account` - Account for which the statements were created. An object implementing `Codelicious\BelgianBankStatement\Account`
  * `InitialBalance` - Balance of the account before the transactions were processed. Up to 3 decimals.
  * `NewBalance` - Balance of the account after the transactions were processed. Up to 3 decimals.
  * `Transactions` - A list of transactions implemented as `Codelicious\BelgianBankStatement\Transaction`
* `Codelicious\BelgianBankStatement\Account`
  * `Name` - Name of the holder of the account
  * `Bic` - Bankcode of the account
  * `Number` - Banknumber of the account
  * `CurrencyCode` - Currency of the account
  * `CountryCode` - Country of the account
* `Codelicious\BelgianBankStatement\Transaction`
  * `Account` - Account of the other party of the transaction. An object implementing `Codelicious\BelgianBankStatement\Account`
  * `TransactionDate` - Date on which the transaction was requested
  * `ValutaDate` - Date on which the transaction was executed by the bank
  * `Amount` - Amount of the transaction. Up to 3 decimals. A negative number for credit transactions.
  * `Message` - Message of the transaction
  * `StructuredMessage` - Structured messages of the transaction (if available)
