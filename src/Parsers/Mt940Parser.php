<?php

namespace Codelicious\BelgianBankStatement\Parsers;


/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Mt940Parser extends AbstractParser {

    public function parse($content)
    {
        $string = implode("\n", $content);

        $parser = new \Kingsquare\Parser\Banking\Mt940();
        $ori_statements = $parser->parse($string);
        $statements = array();

        if ($ori_statements) {
            foreach($ori_statements as $stmt) {
                array_push($statements, $this->convert($stmt));
            }
        }
        return $statements;
    }

    private function convert($stmt)
    {
        $statement = new \Codelicious\BelgianBankStatement\Data\Statement();
        $statement->date = $stmt->getTimestamp('Y-m-d');
        $statement->original_balance = $stmt->getStartPrice();
        $statement->new_balance = $stmt->getEndPrice();
        $statement->account = new \Codelicious\BelgianBankStatement\Data\Account();
        $statement->account->name = $stmt->getAccount();
        $statement->account->number = $stmt->getNumber();

        foreach($stmt->getTransactions() as $tr)
        {
            $transaction = new \Codelicious\BelgianBankStatement\Data\Transaction();
            $transaction->account = new \Codelicious\BelgianBankStatement\Data\Account();

            $transaction->message = $tr->getDescription();
            $transaction->transaction_date = $tr->getEntryTimestamp('Y-m-d');
            $transaction->valuta_date = $tr->getValueTimestamp('Y-m-d');
            $transaction->amount = $tr->getPrice();
            $transaction->account->name = $tr->getAccountName();
            $transaction->account->number = $tr->getAccount();

            array_push($statement->transactions, $transaction);
        }

        return $statement;
    }
}