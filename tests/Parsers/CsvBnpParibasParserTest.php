<?php

namespace Codelicious\Tests\BelgianBankStatement;


class CsvBnpParibasParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSample1()
    {
        $parser = new \Codelicious\BelgianBankStatement\Parsers\CsvBnpParibasParser();

        $statements = $parser->parse($this->getSample1());

        $this->assertEquals(1, count($statements));
        $statement = $statements[0];

        $this->assertNotEmpty($statement->account);
        $this->assertEquals(4, count($statement->transactions));

        $this->assertEquals("BE58 2135 3215 3215", $statement->account->number);

        $tr1 = $statement->transactions[0];
        $tr2 = $statement->transactions[1];
        $tr3 = $statement->transactions[2];
        $tr4 = $statement->transactions[3];

        $this->assertNotEmpty($tr1->account);
        $this->assertEquals("2015-01-13", $tr1->transaction_date);
        $this->assertEquals("2015-01-13", $tr1->valuta_date);
        $this->assertEquals(-5.3, $tr1->amount);
        $this->assertEquals("MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015", $tr1->message);
        $this->assertEmpty($tr1->structured_message);

        $this->assertEquals("BETALING MET BANKKAART", $tr1->account->number);
        $this->assertEquals("EUR", $tr1->account->currency);
        $this->assertEmpty($tr1->account->country);

        $this->assertEquals("BE12 1566 1484 5045", $tr2->account->number);
        $this->assertEquals("NAAM VAN DE KLANT BE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015", $tr2->message);
    }

    private function getSample1()
    {
        $content = array(
        '"JAAR + REFERTE";"UITVOERINGSDATUM";"VALUTADATUM";"BEDRAG";"MUNT V/D REKENING";"TEGENPARTIJ VAN DE VERRICHTING";"DETAILS";"REKENINGNUMMER"',
        '"2015-0124";"13/01/2015";"13/01/2015";"-5,30";"EUR";"BETALING MET BANKKAART";"MET KAART 2131 02XX XXXX X318 2 EEN WINKEL      9000 13-01-2015 VALUTADATUM : 13/01/2015";"BE58 2135 3215 3215 ";',
        '"2015-0118";"11/02/2015";"11/02/2015";"97,57";"EUR";"BE12 1566 1484 5045 ";"NAAM VAN DE KLANT BE15 3215 3215 2185  BIC A32DEDF5 MEDEDELING : dit is een custom message     VALUTADATUM : 11/03/2015";"BE58 2135 3215 3215 ";',
        '"2015-0076";"14/03/2015";"16/03/2015";"-78,48";"EUR";"BE12 5498 2135 2158 ";"SOME SHOP BE21354584321548 BIC VDSDEC21    VIA PC BANKING MEDEDELING : 215456321548 UITGEVOERD OP 13-03 VALUTADATUM : 16/03/2015";"BE58 2135 3215 3215 ";',
        '"2015-0075";"14/04/2015";"16/04/2015";"-80,46";"EUR";"BE15 2135 5148 2133 ";"TELENET NV BE23156489435123 BIC KREDBEBB    VIA PC BANKING MEDEDELING : 321231564845 UITGEVOERD OP 13-04 VALUTADATUM : 16/04/2015";"BE58 2135 3215 3215 ";',
        );

        return $content;
    }

}