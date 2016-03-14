<?php

namespace Codelicious\Tests\BelgianBankStatement;

class Mt940ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSample1()
    {
        $parser = new \Codelicious\BelgianBankStatement\Parsers\Mt940Parser();

        $statements = $parser->parse($this->getSample1());

        $this->assertEquals(1, count($statements));
        $statement = $statements[0];

        $this->assertNotEmpty($statement->account);
        $this->assertEquals(3, count($statement->transactions));
        $this->assertEquals("2010-07-22", $statement->date);
        $this->assertEquals(44.89, $statement->original_balance);
        $this->assertEquals(-9945.09, $statement->new_balance);

        $this->assertEquals("111111111", $statement->account->name);
        $this->assertEquals("100", $statement->account->number);

        $tr1 = $statement->transactions[0];
        $tr2 = $statement->transactions[1];
        $tr3 = $statement->transactions[2];

        $this->assertNotEmpty($tr1->account);
        $this->assertEquals("2010-07-22", $tr1->transaction_date);
        $this->assertEquals("2010-07-22", $tr1->valuta_date);
        $this->assertEquals(0.56, $tr1->amount);
        $this->assertEquals("0111111111 V. DE JONG KERKSTRAAT 1154 1234 BWENSCHEDE BET.KENM. 1004510036716378 3305330802AFLOSSINGSTERMIJN 188616 / 1E TERMIJN", $tr1->message);
        $this->assertEmpty($tr1->structured_message);

        $this->assertEquals("V. DE JONG KERKSTRAAT 1154 1234 BW", $tr1->account->name);
        $this->assertEquals("111111111", $tr1->account->number);

        $this->assertEquals("0111111111 CUSTOMER NL SPOEDBETALINGGE2009120212345RE091202­3737 /RFB/NL­FMI­021209 NL­FMI­021209VOORSCHOTCOMMISSIE", $tr2->message);
        $this->assertEquals("", $tr2->structured_message);

        $this->assertEquals("TOTAAL", $tr3->account->name);
    }

    private function getSample1()
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

        return $content;
    }

}