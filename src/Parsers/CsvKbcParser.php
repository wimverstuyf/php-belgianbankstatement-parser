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
class CsvKbcParser extends CsvParser {
	
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
		if (count($data) < 18) {
			throw new UnexpectedValueException("CSV content invalid");
		}
		
		$account = new Account(trim($data[2]), "", trim($data[0]), trim($data[3]), "");
		
		return [$account, new Transaction(
			new Account(
				trim($data[14]),
				trim($data[13]),
				trim($data[12]),
				(string)$data[3],
				""
			),
			$this->convertDate((string)$data[5]),
			$this->convertDate((string)$data[7]),
			(float)str_replace(',', '.', $data[8]),
			trim($data[17]),
			trim($data[16])
		)];
	}
}