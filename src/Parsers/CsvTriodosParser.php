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
		$date = $dateString;
		if (mb_strlen($dateString) == 10) {
			$date = substr($dateString, 6, 4) . "-" . substr($dateString, 3, 2) . "-" . substr($dateString, 0, 2);
		}

		return new DateTime($date);
	}

	/**
	 * Returns the CSV separator character used in Triodos bank statements
	 * 
	 * @return string The separator character (semicolon)
	 */
	protected function getSeparator(): string
	{
		return ";";
	}

	/**
	 * @param array $data
	 * @return array [Account, Transaction]
	 */
	protected function parseLine(array $data): array
	{
		if (count($data) < 9) {
			throw new UnexpectedValueException("CSV content invalid");
		}

		// Account represents the bank account of the statement owner
		$account = new Account(
			trim($data[1]),    // accountNumber (from column 1)
			"TRIOBEBB",        // bic
			trim($data[5]),    // name (payee from column 5)
			"EUR",             // currency 
			"BE"              // countryCode (since it's Triodos Belgium)
		);

		return [$account, new Transaction(
			new Account(
				trim($data[4]),    // accountNumber (counter-party account)
				"",                // bic
				trim($data[3]),    // name (counter-party name)
				"EUR",             // currency
				""                 // countryCode
			),
			"",                                           // statementLine (not available in CSV)
			$this->convertDate((string)$data[0]),        // transactionDate (column 0)
			$this->convertDate((string)$data[0]),        // valueDate (using same as transaction date)
			(float)str_replace(',', '.', $data[2]),      // amount (column 2)
			(string)$data[8],                            // message (memo from column 8)
			""                                           // extraDetails
		)];
	}
}
