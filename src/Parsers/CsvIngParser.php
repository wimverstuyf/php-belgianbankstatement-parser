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
		if (count($data) < 10) {
			throw new UnexpectedValueException("CSV content invalid");
		}

		$account = new Account(trim($data[1]), "", trim($data[0]), "", "");

		return [$account, new Transaction(
			new Account(
				"",
				"",
				trim($data[2]),
				(string)$data[7],
				""
			),
			trim($data[8]),
			$this->convertDate((string)$data[4]),
			$this->convertDate((string)$data[5]),
			(float)str_replace(',', '.', $data[6]),
			trim($data[10]),
			""
		)];
	}
}
