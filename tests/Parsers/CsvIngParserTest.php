<?php

namespace Codelicious\Tests\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CsvIngParser;

class CsvIngParserTest extends \PHPUnit\Framework\TestCase
{
	public function testSample()
	{
		$parser = new CsvIngParser();

		$statements = $parser->parse($this->getSample());

		$this->assertEquals(1, count($statements));
		$statement = $statements[0];

		$this->assertEquals(2, count($statement->getTransactions()));

		$this->assertEquals("378-1568321-22", $statement->getAccount()->getNumber());
		$this->assertEquals("CODELICIOUS", $statement->getAccount()->getName());
		$this->assertEmpty($statement->getAccount()->getCurrencyCode());

		$tr1 = $statement->getTransactions()[0];
		$tr2 = $statement->getTransactions()[1];

		$this->assertEquals("Betaling Bancontact 21/12/20 - 9.26 uur - LOODGIETER PIET   1000 - BRUSSEL - BEL Kaartnummer 1582 40XX XXXX 1548 2", $tr1->getDescription());
		$this->assertEquals("2020-12-21", $tr1->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2020-12-22", $tr1->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(-151.80, $tr1->getAmount());
		$this->assertEquals("Description5", $tr1->getMessage());
		$this->assertEmpty($tr1->getStructuredMessage());

		$this->assertEquals("184-5135486-18", $tr1->getAccount()->getNumber());
		$this->assertEquals("EUR", $tr1->getAccount()->getCurrencyCode());
		$this->assertEmpty($tr1->getAccount()->getBic());
		$this->assertEmpty($tr1->getAccount()->getName());
		$this->assertEmpty($tr1->getAccount()->getCountryCode());

		$this->assertEquals("2021-02-19", $tr2->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2021-02-18", $tr2->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(552.45, $tr2->getAmount());
		$this->assertEmpty($tr2->getMessage());
		$this->assertEmpty($tr2->getStructuredMessage());
	}

	private function getSample(): string {
		$content = array(
			'Rekeningnummer;Naam van de rekening;Rekening tegenpartij;Omzetnummer;Boekingsdatum;Valutadatum;Bedrag;Munteenheid;Omschrijving;Detail van de omzet;Bericht',
			'378-1568321-22;CODELICIOUS;184-5135486-18;23;21/12/2020;22/12/2020;-151,80;EUR;Betaling Bancontact 21/12/20 - 9.26 uur - LOODGIETER PIET   1000 - BRUSSEL - BEL Kaartnummer 1582 40XX XXXX 1548 2 ;;Description5',
			'378-1568321-22;CODELICIOUS;;35;19/02/2021;18/02/2021;552,45;EUR;Aankoop Maestro  18/02 - 8.30 uur - PATISSERIE PIET Kaartnummer 1322 10XX XXXX 1258 1 Referentie ING: COP00121350055 ;;',
		);

		return implode("\n", $content);
	}
}
