<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Account;
use Codelicious\BelgianBankStatement\Values\Statement;
use Codelicious\BelgianBankStatement\Values\Transaction;
use DateTime;
use Kingsquare\Parser\Banking\Mt940;
use Kingsquare\Banking\Statement as Mt940Statement;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Mt940Parser implements ParserInterface {
	
	/**
	 * @param string $contentToParse
	 * @return Statement[]
	 */
	public function parse(string $contentToParse): array
	{
		$parser = new Mt940();
		$mt940Statements = $parser->parse($contentToParse);
		
		return array_map(
			function(Mt940Statement $statement) {
				return $this->convert($statement);
			}, $mt940Statements);
	}
	
	/**
	 * @param string $fileToParse
	 * @return Statement[]
	 */
	public function parseFile(string $fileToParse): array
	{
		return $this->parse(file_get_contents($fileToParse));
	}
	
	private function convert(Mt940Statement $stmt): Statement
	{
		$transactions = [];
		foreach($stmt->getTransactions() as $tr)
		{
			array_push(
				$transactions,
				new Transaction(
					new Account(
						$tr->getAccountName(),
						"",
						$tr->getAccount(),
						"",
						""
					),
					new DateTime($tr->getEntryTimestamp('Y-m-d')),
					new DateTime($tr->getValueTimestamp('Y-m-d')),
					$tr->getPrice(),
					$tr->getDescription(),
					""
				)
			);
		}
		
		return new Statement(
			new DateTime($stmt->getStartTimestamp('Y-m-d')),
			new Account(
				$stmt->getAccount(),
				"",
				$stmt->getNumber(),
				"",
				""
			),
			$stmt->getStartPrice(),
			$stmt->getEndPrice(),
			$transactions);
	}
}