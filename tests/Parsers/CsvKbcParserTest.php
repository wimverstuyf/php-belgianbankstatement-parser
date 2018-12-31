<?php

namespace Codelicious\Tests\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CsvKbcParser;

class CsvKbcParserTest extends \PHPUnit\Framework\TestCase
{
	public function testSample1()
	{
		$parser = new CsvKbcParser();

		$statements = $parser->parse($this->getSample1());

		$this->assertEquals(1, count($statements));
		$statement = $statements[0];

		$this->assertEquals(4, count($statement->getTransactions()));

		$this->assertEquals("BE15648432165482", $statement->getAccount()->getNumber());
		$this->assertEquals("MyName", $statement->getAccount()->getName());
		$this->assertEquals("EUR", $statement->getAccount()->getCurrencyCode());

		$tr1 = $statement->getTransactions()[0];
		$tr2 = $statement->getTransactions()[1];
		$tr3 = $statement->getTransactions()[2];
		$tr4 = $statement->getTransactions()[3];

		$this->assertEquals("EUROPESE OVERSCHRIJVING VAN          04-01", $tr1->getDescription());
		$this->assertEquals("2016-01-04", $tr1->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2016-01-04", $tr1->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(49, $tr1->getAmount());
		$this->assertEquals("Description5", $tr1->getMessage());
		$this->assertEmpty($tr1->getStructuredMessage());

		$this->assertEquals("BE21 1548 2315 1548", $tr1->getAccount()->getNumber());
		$this->assertEquals("EUR", $tr1->getAccount()->getCurrencyCode());
		$this->assertEquals("ARSPBE11", $tr1->getAccount()->getBic());
		$this->assertEquals("Client1", $tr1->getAccount()->getName());
		$this->assertEmpty($tr1->getAccount()->getCountryCode());

		$this->assertEquals("2016-01-03", $tr2->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2016-01-04", $tr2->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(89.05, $tr2->getAmount());
		$this->assertEquals("Description4", $tr2->getMessage());
		$this->assertEmpty($tr2->getStructuredMessage());

		$this->assertEquals("BE32 3154 1548 1253", $tr2->getAccount()->getNumber());
		$this->assertEquals("EUR", $tr2->getAccount()->getCurrencyCode());
		$this->assertEquals("KREDBEBB", $tr2->getAccount()->getBic());
		$this->assertEquals("Client2", $tr2->getAccount()->getName());
		$this->assertEmpty($tr2->getAccount()->getCountryCode());

		$this->assertEquals(79, $tr3->getAmount());

		$this->assertEquals("2015-12-30", $tr4->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2015-12-30", $tr4->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(62.77, $tr4->getAmount());
		$this->assertEquals("179446761008", $tr4->getStructuredMessage());
		$this->assertEmpty($tr4->getMessage());

		$this->assertEquals("Client4", $tr4->getAccount()->getName());
	}

	private function getSample1()
	{
		$content = array(
			"Rekeningnummer;Rubrieknaam;Naam;Munt;Afschriftnummer;Datum;Omschrijving;Valuta;Bedrag;Saldo;credit;debet;rekeningnummer tegenpartij;BIC tegenpartij;Naam tegenpartij;Adres tegenpartij;gestructureerde mededeling;Vrije mededeling",
			"BE15648432165482;MyReference;MyName;EUR;  02016003;04/01/2016;EUROPESE OVERSCHRIJVING VAN          04-01;04/01/2016;49,00;1289,43;49,00;              ;BE21 1548 2315 1548;ARSPBE11;Client1;Address1;                                   ;Description5",
			"BE15648432165482;MyReference;MyName;EUR;  02016002;03/01/2016;EUROPESE OVERSCHRIJVING VAN          03-01;04/01/2016;89,05;1338,43;89,05;              ;BE32 3154 1548 1253;KREDBEBB;Client2;Address 2;                                   ;Description4",
			"BE15648432165482;MyReference;MyName;EUR;  02016001;02/01/2016;EUROPESE OVERSCHRIJVING VAN          02-01;02/01/2016;79,00;1417,43;79,00;              ;BE21 2315 8432 2315;KREDBEBB;Client3;Address 3;                                   ;Description3",
			"BE15648432165482;MyReference;MyName;EUR;  02015354;30/12/2015;EUROPESE OVERSCHRIJVING VAN          30-12;30/12/2015;62,77;1480,20;62,77;              ;BE21 2315 2158 2315;KREDBEBB;Client4;Address 4;179446761008;"
		);

		return implode("\n", $content);
	}
}
