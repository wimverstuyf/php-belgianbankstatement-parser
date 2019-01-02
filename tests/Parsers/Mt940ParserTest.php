<?php

namespace Codelicious\Tests\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\Mt940Parser;

class Mt940ParserTest extends \PHPUnit\Framework\TestCase
{
	public function testSample1()
	{
		$parser = new Mt940Parser();

		$statements = $parser->parse($this->getSample1());

		$this->assertEquals(1, count($statements));
		$statement = $statements[0];

		$this->assertEquals(3, count($statement->getTransactions()));
		$this->assertEquals("2010-07-22", $statement->getDate()->format('Y-m-d'));
		$this->assertEquals(44.89, $statement->getInitialBalance());
		$this->assertEquals(-9945.09, $statement->getNewBalance());

		$this->assertEquals("111111111", $statement->getAccount()->getName());
		$this->assertEquals("100", $statement->getAccount()->getNumber());

		$tr1 = $statement->getTransactions()[0];
		$tr2 = $statement->getTransactions()[1];
		$tr3 = $statement->getTransactions()[2];

		$this->assertEquals("2010-07-22", $tr1->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2010-07-22", $tr1->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(0.56, $tr1->getAmount());
		$this->assertEquals("0111111111 V. DE JONG KERKSTRAAT 1154 1234 BWENSCHEDE BET.KENM. 1004510036716378 3305330802AFLOSSINGSTERMIJN 188616 / 1E TERMIJN", $tr1->getMessage());
		$this->assertEmpty($tr1->getStructuredMessage());
		$this->assertEmpty($tr1->getDescription());

		$this->assertEquals("V. DE JONG KERKSTRAAT 1154 1234 BW", $tr1->getAccount()->getName());
		$this->assertEquals("111111111", $tr1->getAccount()->getNumber());

		$this->assertEquals("0111111111 CUSTOMER NL SPOEDBETALINGGE2009120212345RE091202­3737 /RFB/NL­FMI­021209 NL­FMI­021209VOORSCHOTCOMMISSIE", $tr2->getMessage());
		$this->assertEquals("", $tr2->getStructuredMessage());

		$this->assertEquals("TOTAAL", $tr3->getAccount()->getName());
	}

	private function getSample1(): string
	{
		$content = array(
			"0000 01INGBNL2AXXXX00001",
			"0000 01INGBNL2AXXXX00001",
			"940 00",
			":20:INGEB",
			":25:0111111111",
			":28C:100",
			":60F:C100722EUR44,89",
			":61:100722C0,56N078NONREF",
			":86:0111111111 V. DE JONG KERKSTRAAT 1154 1234 BW",
			"ENSCHEDE BET.KENM. 1004510036716378 3305330802",
			"AFLOSSINGSTERMIJN 188616 / 1E TERMIJN",
			":61:100722C10,45N077NONREF",
			":86:0111111111 CUSTOMER NL SPOEDBETALING",
			"GE2009120212345",
			"RE091202­3737 /RFB/NL­FMI­021209 NL­FMI­021209",
			"VOORSCHOT",
			"COMMISSIE",
			":61:100722D10000,99N029NONREF",
			":86:VERZAMELBETALING BATCH­ID: 012345 TOTAAL 198",
			"POSTEN",
			":62F:D100723EUR9945,09",
			":64:D100723EUR9945,09",
			":65:D100724EUR9945,09",
			":65:D100726EUR9945,09",
			":86 :D000001C000002D10000,99C11,01",
			"ING TESTREKENING",
			"XXX"
		);

		return implode("\n", $content);
	}

}
