<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Account;
use Codelicious\BelgianBankStatement\Values\Statement;
use Codelicious\BelgianBankStatement\Values\Transaction;
use DateTime;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class CsvBnpParibasParser implements ParserInterface {
	
	/**
	 * @param string $contentToParse
	 * @return Statement[]
	 */
	public function parse(string $contentToParse): array
	{
		$path = 'php://memory';
		$h = fopen($path, "rw+");
		fwrite($h, $contentToParse);
		fseek($h, 0);
		
		$statement = $this->parseFileHandle($h);
		
		fclose($h);
		
		return [$statement];
	}
	
	/**
	 * @param string $fileToParse
	 * @return Statement[]
	 */
	public function parseFile(string $fileToParse): array
	{
		return $this->parse(file_get_contents($fileToParse));
	}
	
	private function parseFileHandle($handle)
	{
		// credits: based on https://github.com/robarov/csv2mt940
		
		$transactions = [];
		$isFirstLine = true;
		$accountNumber = "";
		while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {
			if ($isFirstLine) {
				// We don't need the first row, as it contains the column headers
				$isFirstLine = false;
			} else {
				$accountNumber = trim($data[7]);
				
				array_push(
					$transactions,
					new Transaction(
						new Account(
							"",
							"",
							trim($data[5]),
							$data[4],
							""
						),
						$this->convertDate($data[1]),
						$this->convertDate($data[2]),
						(float)str_replace(',', '.', $data[3]),
						$data[6],
						""
					)
				);
			}
		}
		
		return new Statement(
			new DateTime("0001-01-01"),
			new Account("", "", $accountNumber, "", ""),
			0,
			0,
			$transactions
		);
	}
	
	private function convertDate($dateString): DateTime
	{
		$date = $dateString;
		if (mb_strlen($dateString) == 10) {
			$date = substr($dateString, 6, 4) . "-" . substr($dateString, 3, 2) . "-" . substr($dateString, 0, 2);
		}
		
		return new DateTime($date);
	}
}