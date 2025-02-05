<?php

namespace Codelicious\Tests\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CsvCrelanParser;

class CsvCrelanParserTest extends \PHPUnit\Framework\TestCase
{
	public function testSample()
	{
		$parser = new CsvCrelanParser();
		$statements = $parser->parse($this->getSample());

		$this->assertEquals(1, count($statements));
		$statement = $statements[0];

		$this->assertEquals(4, count($statement->getTransactions()));

		// Test first transaction
		$tr1 = $statement->getTransactions()[0];
		$this->assertEquals("Paiement Bancontact contactless", $tr1->getDescription());
		$this->assertEquals("2025-02-03", $tr1->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2025-02-03", $tr1->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(-5.95, $tr1->getAmount());
		$this->assertEquals("STORE 1234 03-02-2025 12:06 LOCATION A 123456******1234", $tr1->getMessage());
		
		// Account details for first transaction
		$this->assertEquals("EUR", $tr1->getAccount()->getCurrencyCode());
		$this->assertEquals("STORE 1234 LOCATION A", $tr1->getAccount()->getName());
		
		// Test second transaction
		$tr2 = $statement->getTransactions()[1];
		$this->assertEquals("Paiement Bancontact contactless", $tr2->getDescription());
		$this->assertEquals(-0.80, $tr2->getAmount());
		$this->assertEquals("STORE 5678 03-02-2025 16:09 LOCATION B 123456******1234", $tr2->getMessage());
		$this->assertEquals("STORE 5678 LOCATION B", $tr2->getAccount()->getName());
		
		// Test third transaction
		$tr3 = $statement->getTransactions()[2];
		$this->assertEquals("Paiement Bancontact contactless", $tr3->getDescription());
		$this->assertEquals(-9.50, $tr3->getAmount());
		$this->assertEquals("SHOP 9012 LOCATION C", $tr3->getAccount()->getName());
		
		// Test fourth transaction
		$tr4 = $statement->getTransactions()[3];
		$this->assertEquals("Virement via Crelan Mobile", $tr4->getDescription());
		$this->assertEquals(-20.00, $tr4->getAmount());
		$this->assertEquals("12022025+Transfer+ANONYMOUS", $tr4->getMessage());
		$this->assertEquals("Person A", $tr4->getAccount()->getName());
		$this->assertEquals("BE11 1111 2222 3333", $tr4->getAccount()->getNumber());
	}

	private function getSample()
	{
		$content = array(
			'Date;Montant;Solde apres operation;Devise;Contrepartie;Compte contrepartie;Type d\'operation;Communication;Compte donneur d\'ordre',
			'03/02/2025;-5.95;2100.50;EUR;STORE 1234 LOCATION A;;Paiement Bancontact contactless;STORE 1234 03-02-2025 12:06 LOCATION A 123456******1234;BE00 0000 1111 2222',
			'03/02/2025;-0.80;2099.70;EUR;STORE 5678 LOCATION B;;Paiement Bancontact contactless;STORE 5678 03-02-2025 16:09 LOCATION B 123456******1234;BE00 0000 1111 2222',
			'04/02/2025;-9.50;2090.20;EUR;SHOP 9012 LOCATION C;;Paiement Bancontact contactless;SHOP 9012 04-02-2025 12:16 LOCATION C 123456******1234;BE00 0000 1111 2222',
			'04/02/2025;-20.00;2070.20;EUR;Person A;BE11 1111 2222 3333;Virement via Crelan Mobile;12022025+Transfer+ANONYMOUS;BE00 0000 1111 2222'
		);

		return implode("\n", $content);
	}
}
