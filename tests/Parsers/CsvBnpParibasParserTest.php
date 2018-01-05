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
		
		$this->assertEquals("BE58 2135 3215 3215", $statement->getAccount()->getNumber());
		
		$tr1 = $statement->getTransactions()[0];
		$tr2 = $statement->getTransactions()[1];
		$tr3 = $statement->getTransactions()[2];
		$tr4 = $statement->getTransactions()[3];
		
		$this->assertEquals("2015-01-13", $tr1->getTransactionDate()->format('Y-m-d'));
		$this->assertEquals("2015-01-13", $tr1->getValutaDate()->format('Y-m-d'));
		$this->assertEquals(-5.3, $tr1->getAmount());
		$this->assertEquals("MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015", $tr1->getMessage());
		$this->assertEmpty($tr1->getStructuredMessage());
		
		$this->assertEquals("BETALING MET BANKKAART", $tr1->getAccount()->getNumber());
		$this->assertEquals("EUR", $tr1->getAccount()->getCurrencyCode());
		$this->assertEmpty($tr1->getAccount()->getCountryCode());
		
		$this->assertEquals("BE12 1566 1484 5045", $tr2->getAccount()->getNumber());
		$this->assertEquals("NAAM VAN DE KLANT BE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015", $tr2->getMessage());
	}
	
	/**
	 * @expectedException        UnexpectedValueException
	 * @expectedExceptionMessage CSV content invalid
	 */
	public function testInvalidSample()
	{
		$parser = new CsvBnpParibasParser();
		
		$parser->parse($this->getInvalidSample());
	}
	
	private function getSample1(): string
	{
		$content = array(
			'"JAAR + REFERTE";"UITVOERINGSDATUM";"VALUTADATUM";"BEDRAG";"MUNT V/D REKENING";"TEGENPARTIJ VAN DE VERRICHTING";"DETAILS";"REKENINGNUMMER"',
			'"2015-0124";"13/01/2015";"13/01/2015";"-5,30";"EUR";"BETALING MET BANKKAART";"MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015";"BE58 2135 3215 3215 ";',
			'"2015-0118";"11/02/2015";"11/02/2015";"97,57";"EUR";"BE12 1566 1484 5045 ";"NAAM VAN DE KLANT BE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015";"BE58 2135 3215 3215 ";',
			'"2015-0076";"14/03/2015";"16/03/2015";"-78,48";"EUR";"BE12 5498 2135 2158 ";"SOME SHOP BE21354584321548 BIC VDSDEC21    VIA PC BANKING MEDEDELING : 215456321548 UITGEVOERD OP 13-03 VALUTADATUM : 16/03/2015";"BE58 2135 3215 3215 ";',
			'"2015-0075";"14/04/2015";"16/04/2015";"-80,46";"EUR";"BE15 2135 5148 2133 ";"TELENET NV BE23156489435123 BIC KREDBEBB    VIA PC BANKING MEDEDELING : 321231564845 UITGEVOERD OP 13-04 VALUTADATUM : 16/04/2015";"BE58 2135 3215 3215 ";',
		);
		
		return implode("\n", $content);
	}
	
	private function getInvalidSample(): string
	{
		$content = array(
			'"JAAR + REFERTE"\t"UITVOERINGSDATUM"\t"VALUTADATUM"\t"BEDRAG"\t"MUNT V/D REKENING"\t"TEGENPARTIJ VAN DE VERRICHTING"\t"DETAILS"\t"REKENINGNUMMER"',
			'"2015-0124"\t"13/01/2015"\t"13/01/2015"\t"-5,30"\t"EUR"\t"BETALING MET BANKKAART"\t"MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015"\t"BE58 2135 3215 3215 "\t',
			'"2015-0118"\t"11/02/2015"\t"11/02/2015"\t"97,57"\t"EUR"\t"BE12 1566 1484 5045 "\t"NAAM VAN DE KLANT BE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015"\t"BE58 2135 3215 3215 "\t',
			'"2015-0076"\t"14/03/2015"\t"16/03/2015"\t"-78,48"\t"EUR"\t"BE12 5498 2135 2158 "\t"SOME SHOP BE21354584321548 BIC VDSDEC21    VIA PC BANKING MEDEDELING : 215456321548 UITGEVOERD OP 13-03 VALUTADATUM : 16/03/2015"\t"BE58 2135 3215 3215 "\t',
			'"2015-0075"\t"14/04/2015"\t"16/04/2015"\t"-80,46"\t"EUR"\t"BE15 2135 5148 2133 "\t"TELENET NV BE23156489435123 BIC KREDBEBB    VIA PC BANKING MEDEDELING : 321231564845 UITGEVOERD OP 13-04 VALUTADATUM : 16/04/2015"\t"BE58 2135 3215 3215 "\t',
		);
		
		return implode("\n", $content);
	}
}