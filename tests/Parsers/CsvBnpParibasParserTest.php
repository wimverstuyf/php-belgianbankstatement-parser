<?php

namespace Codelicious\Tests\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CsvBnpParibasParser;
use UnexpectedValueException;

class CsvBnpParibasParserTest extends \PHPUnit\Framework\TestCase
{
	public function testSample1()
	{
		$parser = new CsvBnpParibasParser();

		$statements = $parser->parse($this->getSample1());

		$this->assertEquals(1, count($statements));
		$statement = $statements[0];

		$this->assertEquals(4, count($statement->getTransactions()));

		$this->assertEquals("BE58213532153215", $statement->getAccount()->getNumber());

		$tr1 = $statement->getTransactions()[0];
		$tr2 = $statement->getTransactions()[1];
		$tr3 = $statement->getTransactions()[2];
		$tr4 = $statement->getTransactions()[3];

		$this->assertEquals("2015-01-13", $tr1->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2015-01-13", $tr1->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(-5.3, $tr1->getAmount());
		$this->assertEquals("515485251", $tr1->getMessage());
		$this->assertEmpty($tr1->getStructuredMessage());
		$this->assertEmpty($tr1->getDescription());

		$this->assertEquals("BETALING MET BANKKAART", $tr1->getAccount()->getNumber());
		$this->assertEquals("EUR", $tr1->getAccount()->getCurrencyCode());
		$this->assertEmpty($tr1->getAccount()->getCountryCode());

		$this->assertEquals("BE12156614845045", $tr2->getAccount()->getNumber());
		$this->assertEquals("dit is een custom message", $tr2->getMessage());
	}

	public function testInvalidSample()
	{
		$this->expectExceptionMessage('CSV content invalid');
		$this->expectException(UnexpectedValueException::class);

		$parser = new CsvBnpParibasParser();

		$parser->parse($this->getInvalidSample());
	}

	private function getSample1(): string
	{
		$content = array(
			'Volgnummer;Uitvoeringsdatum;Valutadatum;Bedrag;Valuta rekening;Rekeningnummer;Type verrichting;Tegenpartij;Naam van de tegenpartij;Mededeling;Details;Status;Reden van weigering',
			'',
			'2015-0124;13/01/2015;13/01/2015;-5,30;EUR;BE58213532153215;Betaling met bankkaart;BETALING MET BANKKAART;EEN WINKEL;515485251;MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015;Geaccepteerd;',
			'2015-0118;11/02/2015;11/02/2015;97,57;EUR;BE58213532153215;Overschrijving in euro;BE12156614845045;NAAM VAN DE KLANT;dit is een custom message;BE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015;Geaccepteerd;',
			'2015-0076;14/03/2015;16/03/2015;-78,48;EUR;BE58213532153215;Overschrijving in euro;BE12549821352158;SOME SHOP;215456321548;BE21354584321548 BIC VDSDEC21    VIA PC BANKING MEDEDELING : 215456321548 UITGEVOERD OP 13-03 VALUTADATUM : 16/03/2015;Geaccepteerd;',
			'2015-0075;14/04/2015;16/04/2015;-80,46;EUR;BE58213532153215;Overschrijving in euro;BE15213551482133;TELENET NV;321231564845; BE23156489435123 BIC KREDBEBB    VIA PC BANKING MEDEDELING : 321231564845 UITGEVOERD OP 13-04 VALUTADATUM : 16/04/2015;Geaccepteerd;'
		);

		return implode("\n", $content);
	}

	private function getInvalidSample(): string
	{
		$content = array(
			'Volgnummer\tUitvoeringsdatum\tValutadatum\tBedrag\tValuta rekening\tRekeningnummer\tType verrichting\tTegenpartij\tNaam van de tegenpartij\tMededeling\tDetails\tStatus\tReden van weigering',
			'',
			'2015-0124\t13/01/2015\t13/01/2015\t-5,30\tEUR\tBE58213532153215\tBetaling met bankkaart\tBETALING MET BANKKAART\tEEN WINKEL\t515485251\tMET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015\tGeaccepteerd\t',
			'2015-0118\t11/02/2015\t11/02/2015\t97,57\tEUR\tBE58213532153215\tOverschrijving in euro\tBE12156614845045\tNAAM VAN DE KLANT\tdit is een custom message\tBE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015\tGeaccepteerd\t',
			'2015-0076\t14/03/2015\t16/03/2015\t-78,48\tEUR\tBE58213532153215\tOverschrijving in euro\tBE12549821352158\tSOME SHOP\t215456321548\tBE21354584321548 BIC VDSDEC21    VIA PC BANKING MEDEDELING : 215456321548 UITGEVOERD OP 13-03 VALUTADATUM : 16/03/2015\tGeaccepteerd\t',
			'2015-0075\t14/04/2015\t16/04/2015\t-80,46\tEUR\tBE58213532153215\tOverschrijving in euro\tBE15213551482133\tTELENET NV\t321231564845\t BE23156489435123 BIC KREDBEBB    VIA PC BANKING MEDEDELING : 321231564845 UITGEVOERD OP 13-04 VALUTADATUM : 16/04/2015\tGeaccepteerd\t'
		);

		return implode("\n", $content);
	}
}
