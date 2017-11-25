<?php

namespace Codelicious\Tests\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CodaParser;

class CodaParserTest extends \PHPUnit\Framework\TestCase
{
    public function testSample1()
    {
        $parser = new CodaParser();

        $statements = $parser->parse($this->getSample1());

        $this->assertEquals(1, count($statements));
        $statement = $statements[0];

        $this->assertEquals(3, count($statement->getTransactions()));
        $this->assertEquals("2015-01-18", $statement->getDate()->format("Y-m-d"));
        $this->assertEquals(4004.1, $statement->getInitialBalance());
        $this->assertEquals(-500012.1, $statement->getNewBalance());

        $this->assertEquals("CODELICIOUS", $statement->getAccount()->getName());
        $this->assertEquals("GEBABEBB", $statement->getAccount()->getBic());
        $this->assertEquals("001548226815", $statement->getAccount()->getNumber());
        $this->assertEquals("EUR", $statement->getAccount()->getCurrencyCode());
        $this->assertEquals("BE", $statement->getAccount()->getCountryCode());

        $tr1 = $statement->getTransactions()[0];
        $tr2 = $statement->getTransactions()[1];
        $tr3 = $statement->getTransactions()[2];

        $this->assertEquals("2014-12-25", $tr1->getTransactionDate()->format('Y-m-d'));
        $this->assertEquals("2014-12-25", $tr1->getValutaDate()->format('Y-m-d'));
        $this->assertEquals(-767.823, $tr1->getAmount());
        $this->assertEquals("112/4554/46812   813  ANOTHER MESSAGE  MESSAGE", $tr1->getMessage());
        $this->assertEmpty($tr1->getStructuredMessage());

        $this->assertEquals("BVBA.BAKKER PIET", $tr1->getAccount()->getName());
        $this->assertEquals("GEBCEEBB", $tr1->getAccount()->getBic());
        $this->assertEquals("BE54805480215856", $tr1->getAccount()->getNumber());
        $this->assertEquals("EUR", $tr1->getAccount()->getCurrencyCode());
        $this->assertEmpty($tr1->getAccount()->getCountryCode());

        $this->assertEquals("ANOTHER MESSAGE  MESSAGE", $tr2->getMessage());
        $this->assertEquals("112455446812", $tr2->getStructuredMessage());

        $this->assertEmpty($tr3->getAccount()->getName());
        $this->assertEquals("GEBCEEBB", $tr3->getAccount()->getBic());
    }

    private function getSample1(): string
    {
        $content = array(
            "0000018011520105        0938409934CODELICIOUS               GEBABEBB   09029308273 00001          984309          834080       2",
            "10155001548226815 EUR0BE                  0000000004004100241214CODELICIOUS               PROFESSIONAL ACCOUNT               255",
            "21000100000001200002835        1000000000767823251214001120000112/4554/46812   813                                 25121421401 0",
            "2200010000  ANOTHER MESSAGE                                           54875                       GEBCEEBB                   1 0",
            "2300010000BE54805480215856                  EURBVBA.BAKKER PIET                         MESSAGE                              0 1",
            "31000100010007500005482        004800001001BVBA.BAKKER PIET                                                                  1 0",
            "3200010001MAIN STREET 928                    5480 SOME CITY                                                                  0 0",
            "3300010001SOME INFORMATION ABOUT THIS TRANSACTION                                                                            0 0",
            "21000200000001200002835        0000000002767820251214001120001101112455446812  813                                 25121421401 0",
            "2200020000  ANOTHER MESSAGE                                           54875                       GEBCEEBB                   1 0",
            "2300020000BE54805480215856                  EURBVBA.BAKKER PIET                         MESSAGE                              0 1",
            "31000200010007500005482        004800001001BVBA.BAKKER PIET                                                                  1 0",
            "21000900000001200002835        0000000001767820251214001120000112/4554/46812   813                                 25121421401 0",
            "2200090000  ANOTHER MESSAGE                                           54875                       GEBCEEBB                   1 0",
            "8225001548226815 EUR0BE                  1000000500012100120515                                                                0",
            "4 00010005                      THIS IS A PUBLIC MESSAGE                                                                       0",
            "9               000015000000016837520000000003967220                                                                           1",
        );

        return implode("\n", $content);
    }
}
