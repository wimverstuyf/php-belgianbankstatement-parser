<?php

namespace Codelicious\Tests\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CsvTriodosParser;

class CsvTriodosParserTest extends \PHPUnit\Framework\TestCase
{
	public function testSample()
	{
		$parser = new CsvTriodosParser();
		$statements = $parser->parse($this->getSample());

		$this->assertEquals(1, count($statements));
		$statement = $statements[0];

		$this->assertEquals(6, count($statement->getTransactions()));

		// Test first transaction
		$tr1 = $statement->getTransactions()[0];
		$this->assertEquals("SCT", $tr1->getDescription());
		$this->assertEquals("2024-05-21", $tr1->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2024-05-21", $tr1->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(480.00, $tr1->getAmount());
		$this->assertEquals("Contribution John - Jane", $tr1->getMessage());
		
		// Account details for first transaction
		$this->assertEquals("BE22222222222222", $tr1->getAccount()->getNumber());
		$this->assertEquals("EUR", $tr1->getAccount()->getCurrencyCode());
		$this->assertEquals("AXABBE22", $tr1->getAccount()->getBic());
		$this->assertEquals("John Smith - Jane Doe", $tr1->getAccount()->getName());
		
		// Test second transaction
		$tr2 = $statement->getTransactions()[1];
		$this->assertEquals("SCT", $tr2->getDescription());
		$this->assertEquals(264.00, $tr2->getAmount());
		$this->assertEquals("Contribution FDR 2024 Johnson 1st floor", $tr2->getMessage());
		$this->assertEquals("Mark Johnson", $tr2->getAccount()->getName());
		$this->assertEquals("BE33333333333333", $tr2->getAccount()->getNumber());
		
		// Test third transaction
		$tr3 = $statement->getTransactions()[2];
		$this->assertEquals("SCTIB", $tr3->getDescription());
		$this->assertEquals(-74.74, $tr3->getAmount());
		$this->assertEquals("Electrabel", $tr3->getAccount()->getName());
		$this->assertEquals("BE44444444444444", $tr3->getAccount()->getNumber());
		
		// Test fourth transaction
		$tr4 = $statement->getTransactions()[3];
		$this->assertEquals(24.00, $tr4->getAmount());
		$this->assertEquals("Additional contribution 2024 Johnson 1st floor", $tr4->getMessage());
		$this->assertEquals("Mark Johnson", $tr4->getAccount()->getName());
		
		// Test fifth transaction
		$tr5 = $statement->getTransactions()[4];
		$this->assertEquals(432.00, $tr5->getAmount());
		$this->assertEquals("Sa", $tr5->getMessage());
		$this->assertEquals("Mrs Sarah Wilson", $tr5->getAccount()->getName());
		$this->assertEquals("BE55555555555555", $tr5->getAccount()->getNumber());
		$this->assertEquals("BBRUBEBB", $tr5->getAccount()->getBic());
		
		// Test sixth transaction
		$tr6 = $statement->getTransactions()[5];
		$this->assertEquals(-977.43, $tr6->getAmount());
		$this->assertEquals("142/1234/56789", $tr6->getMessage());
		$this->assertEquals("AG Insurance", $tr6->getAccount()->getName());
		$this->assertEquals("BE66666666666666", $tr6->getAccount()->getNumber());
	}

	private function getSample()
	{
		$content = array(
			'"Date","Account","Amount","CounterpartyAccount","BIC","CounterpartyName","Address","TransactionType","Communication","Balance"',
			'"21-05-2024","BE11111111111111","480,00","BE22222222222222","AXABBE22","John Smith - Jane Doe","Example Street 123 1000 BRUSSELS","SCT","Contribution John - Jane","497,98"',
			'"21-05-2024","BE11111111111111","264,00","BE33333333333333","AXABBE22","Mark Johnson","Example Street 123 1000 BRUSSELS","SCT","Contribution FDR 2024 Johnson 1st floor","761,98"',
			'"21-05-2024","BE11111111111111","-74,74","BE44444444444444","GEBABEBB","Electrabel"," ","SCTIB","321/5678/90876","687,24"',
			'"24-05-2024","BE11111111111111","24,00","BE33333333333333","AXABBE22","Mark Johnson","Example Street 123 1000 BRUSSELS","SCT","Additional contribution 2024 Johnson 1st floor","711,24"',
			'"12-06-2024","BE11111111111111","432,00","BE55555555555555","BBRUBEBB","Mrs Sarah Wilson","Example Street 123 1000 BRUSSELS","SCT","Sa","1.143,24"',
			'"12-06-2024","BE11111111111111","-977,43","BE66666666666666","GEBABEBB","AG Insurance"," ","SCTIB","142/1234/56789","165,81"'
		);

		return implode("\n", $content);
	}
}
