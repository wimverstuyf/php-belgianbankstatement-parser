<?php
/**
 * Created by PhpStorm.
 * User: wim
 * Date: 13/03/15
 * Time: 19:54
 */

namespace Codelicious\BelgianBankStatement\Parsers;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class CsvBnpParibasParser extends AbstractParser {

    public function parse($content)
    {
        require('../../vendor/robarov/csv2mt940/classes/CsvParser.php');
        require('../../vendor/robarov/csv2mt940/classes/Transaction.php');

        $path = 'php://memory';
        $h = fopen($path, "rw+");
        fwrite($h, $content);
        fseek($h, 0);

        $parser = new CsvParser();
        $transactions = $parser->parse($h);

        return $this->convert($transactions);
    }

    private function convert($transactions)
    {
        $statement = new \Codelicious\BelgianBankStatement\Data\Statement();

        foreach($transactions as $tr)
        {
            $transaction = new \Codelicious\BelgianBankStatement\Data\Transaction();
            $transaction->account = new \Codelicious\BelgianBankStatement\Data\Account();

            $transaction->message = $tr->getReferte() . " " . $tr->getDetail();
            $transaction->transaction_date = $tr->getUitvoeringsdatum();
            $transaction->valuta_date = $tr->getValutadatum();
            $transaction->amount = $tr->getBedrag();
            $transaction->account->name = $tr->getTegenpartij();
            $transaction->account->number = $tr->getRekening();
            $transaction->account->currency = $tr->getMunt();

            array_push($statement->transactions, $tr);
        }

        return $statement;
    }
}