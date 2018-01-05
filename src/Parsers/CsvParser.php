<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Account;
use Codelicious\BelgianBankStatement\Values\Statement;
use Codelicious\BelgianBankStatement\Values\Transaction;
use DateTime;
use UnexpectedValueException;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
abstract class CsvParser implements ParserInterface {
	
	/**
	 * @param string $contentToParse
	 * @return Statement[]
	 * @throws UnexpectedValueException
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
	 * @throws UnexpectedValueException
	 */
	public function parseFile(string $fileToParse): array
	{
		return $this->parse(file_get_contents($fileToParse));
	}
	
	abstract protected function getSeparator(): string;
	abstract protected function parseLine(array $data): array;
	
	/**
	 * @param $handle
	 * @return Statement
	 * @throws UnexpectedValueException
	 */
	private function parseFileHandle($handle): Statement
	{
		$transactions = [];
		$isFirstLine = true;
		$account = new Account("", "", "", "", "");
		while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {
			if ($isFirstLine) {
				// We don't need the first row, as it contains the column headers
				$isFirstLine = false;
			} else {
				/** @var $lineAccount Account */
				list($lineAccount, $transaction) = $this->parseLine($data);
				
				array_push(
					$transactions,
					$transaction
				);
				
				if ($lineAccount->getName() || $lineAccount->getBic() ||
					$lineAccount->getNumber() || $lineAccount->getCountryCode() ||
					$lineAccount->getCurrencyCode()) {
					
					$account = new Account(
						$lineAccount->getName()?:$account->getName(),
						$lineAccount->getBic()?:$account->getBic(),
						$lineAccount->getNumber()?:$account->getNumber(),
						$lineAccount->getCurrencyCode()?:$account->getCurrencyCode(),
						$lineAccount->getCountryCode()?:$account->getCountryCode()
					);
				}
			}
		}
		
		return new Statement(
			new DateTime("0001-01-01"),
			$account,
			0,
			0,
			$transactions
		);
	}
}