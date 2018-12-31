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
class CsvBnpParibasParser extends CsvParser {

	private function convertDate($dateString): DateTime
	{
		$date = $dateString;
		if (mb_strlen($dateString) == 10) {
			$date = substr($dateString, 6, 4) . "-" . substr($dateString, 3, 2) . "-" . substr($dateString, 0, 2);
		}

		return new DateTime($date);
	}

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
		if (count($data) < 8) {
			throw new UnexpectedValueException("CSV content invalid");
		}

		$account = new Account("", "", trim($data[7]), "", "");

		return [$account, new Transaction(
			new Account(
				"",
				"",
				trim($data[5]),
				(string)$data[4],
				""
			),
			'',
			$this->convertDate((string)$data[1]),
			$this->convertDate((string)$data[2]),
			(float)str_replace(',', '.', $data[3]),
			(string)$data[6],
			""
		)];
	}
}
