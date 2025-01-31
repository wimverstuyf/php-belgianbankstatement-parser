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
		if (count($data) < 10) {
			throw new UnexpectedValueException("CSV content invalid");
		}

		$account = new Account(
			"",                // accountNumber
			"",                // bic
			trim($data[5]),    // name
			"",                // currency
			""                 // countryCode
		);

		return [$account, new Transaction(
			new Account(
				trim($data[8]),    // accountNumber
				"",                // bic
				trim($data[7]),    // name
				(string)$data[4],  // currency
				""                 // countryCode
			),
			(string)$data[10],                           // statementLine
			$this->convertDate((string)$data[1]),        // transactionDate
			$this->convertDate((string)$data[2]),        // valueDate
			(float)str_replace(',', '.', $data[3]),      // amount
			(string)$data[9],                            // message
			""                                           // extraDetails
		)];
	}
}
