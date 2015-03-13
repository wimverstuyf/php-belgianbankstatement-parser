<?php
/**
 * Created by PhpStorm.
 * User: wim
 * Date: 13/03/15
 * Time: 19:53
 */

namespace Codelicious\BelgianBankStatement\Parsers;


/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Mt940Parser extends AbstractParser {

    public function parse($content)
    {
        $parser = new \Kingsquare\Parser\Banking\Mt940();
        $statement = $parser->parse($content);
        return $this->convert($statement);
    }

    private function convert($stmt)
    {
        $statement = new \Codelicious\BelgianBankStatement\Data\Statement();
        $statement->date = $stmt->getTimestamp();
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
            $transaction->transaction_date = $tr->getEntryTimestamp();
            $transaction->valuta_date = $tr->getValueTimestamp();
            $transaction->amount = $tr->getPrice();
            $transaction->account->name = $tr->getAccountName();
            $transaction->account->number = $tr->getAccount();

            array_push($statement->transactions, $transaction);
        }

        return $statement;
    }
}