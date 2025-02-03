<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Account;
use Codelicious\BelgianBankStatement\Values\Transaction;
use DateTime;
use UnexpectedValueException;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class CsvIngParser extends CsvParser {

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
	 * Returns the CSV separator character used in ING bank statements
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
			trim($data[1]),    // accountNumber
			"",                // bic
			trim($data[0]),    // name
			"",                // currency
			""                 // countryCode
		);

		return [$account, new Transaction(
			new Account(
				"",                // accountNumber
				"",                // bic
				trim($data[2]),    // name
				(string)$data[7],  // currency
				""                 // countryCode
			),
			trim($data[8]),                             // description
			$this->convertDate((string)$data[4]),       // transactionDate
			$this->convertDate((string)$data[5]),       // valueDate
			(float)str_replace(',', '.', $data[6]),     // amount
			trim($data[10]),                            // message
			""                                          // structuredMessage
		)];
	}
}
