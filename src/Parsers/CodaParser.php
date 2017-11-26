<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Account;
use Codelicious\BelgianBankStatement\Values\Statement;
use Codelicious\BelgianBankStatement\Values\Transaction;
use Codelicious\Coda\Parser;
use Codelicious\Coda\Statements\Statement as CodaStatement;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class CodaParser implements ParserInterface {
	
	
	/**
	 * @param string $contentToParse
	 * @return Statement[]
	 */
	public function parse(string $contentToParse): array
	{
		$contentAsArray = explode("\n", str_replace("\r", "", $contentToParse));
		$parser = new Parser();
		$codaStatements = $parser->parse($contentAsArray);
		
		return array_map(
			function(CodaStatement $statement) {
				return $this->convert($statement);
			}, $codaStatements);
	}
	
	/**
	 * @param string $fileToParse
	 * @return Statement[]
	 */
	public function parseFile(string $fileToParse): array
	{
		$parser = new Parser();
		$codaStatements = $parser->parseFile($fileToParse);
		
		return array_map(
			function(CodaStatement $statement) {
				return $this->convert($statement);
			}, $codaStatements);
	}
	
	private function convert(CodaStatement $statement)
	{
		$transactions = [];
		foreach($statement->getTransactions() as $transaction) {
			array_push(
				$transactions,
				new Transaction(
					new Account(
						$transaction->getAccount()->getName(),
						$transaction->getAccount()->getBic(),
						$transaction->getAccount()->getNumber(),
						$transaction->getAccount()->getCurrencyCode(),
						""
					),
					$transaction->getTransactionDate(),
					$transaction->getValutaDate(),
					$transaction->getAmount(),
					$transaction->getMessage(),
					$transaction->getStructuredMessage()
				)
			);
		}
		
		return new Statement(
			$statement->getDate(),
			new Account(
				$statement->getAccount()->getName(),
				$statement->getAccount()->getBic(),
				$statement->getAccount()->getNumber(),
				$statement->getAccount()->getCurrencyCode(),
				$statement->getAccount()->getCountryCode()
			),
			$statement->getInitialBalance(),
			$statement->getNewBalance(),
			$transactions);
	}
}