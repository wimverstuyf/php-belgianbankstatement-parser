<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Account;
use Codelicious\BelgianBankStatement\Values\Transaction;
use DateTime;
use UnexpectedValueException;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @author Cyrille Duverne (cydit.now@gmail.com)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class CsvTriodosParser extends CsvParser {

	/**
	 * Converts a date string from DD/MM/YYYY format to YYYY-MM-DD format
	 * 
	 * @param string $dateString The date string to convert (expected format: DD/MM/YYYY)
	 * @return DateTime Returns a DateTime object representing the parsed date
	 */
	private function convertDate($dateString): DateTime
	{
		if (mb_strlen($dateString) == 10) {
			// Convert from DD-MM-YYYY to YYYY-MM-DD
			$parts = explode('-', $dateString);
			if (count($parts) === 3) {
				return new DateTime($parts[2] . '-' . $parts[1] . '-' . $parts[0]);
			}
		}
		throw new UnexpectedValueException("Invalid date format: " . $dateString);
	}

	/**
	 * Returns the CSV separator character used in Triodos bank statements
	 * 
	 * @return string The separator character (semicolon)
	 */
	protected function getSeparator(): string
	{
		return ",";
	}

	/**
	 * @param array $data
	 * @return array [Account, Transaction]
	 */
	protected function parseLine(array $data): array
	{
		if (count($data) < 10) {
			throw new UnexpectedValueException("CSV content invalid");
		}

		// Account represents the bank account of the statement owner
		$account = new Account(
			"",                // name (not provided for account owner)
			"",                // bic (not provided for account owner)
			trim($data[1]),    // number (from column 1: BE)
			"EUR",             // currencyCode 
			"BE"               // countryCode
		);

		// Create transaction with counter-party account details
		$transaction = new Transaction(
			new Account(
				trim($data[5]),    // name (counter-party name, column 5)
				trim($data[4]),    // bic (counter-party BIC, column 4)
				trim($data[3]),    // number (counter-party account, column 3)
				"EUR",             // currencyCode
				""                 // countryCode
			),
			trim($data[7]),                               // statementLine
			$this->convertDate((string)$data[0]),         // transactionDate
			$this->convertDate((string)$data[0]),         // valueDate
			(float)str_replace(',', '.', $data[2]),       // amount
			trim($data[8]),                               // message
			trim($data[6])                                // extraDetails
		);

		return [$account, $transaction];
	}
}
