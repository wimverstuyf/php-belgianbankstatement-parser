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
class CodaParser extends AbstractParser {

    public function parse($content)
    {
        $parser = new \Codelicious\Coda\Parser();
        $statements = $parser->parse($content, "simple");

        if ($statements)
            return $this->convert($statements[0]);
        else
            return NULL;
    }

    private function convert($stmt)
    {
        $statement = new \Codelicious\BelgianBankStatement\Data\Statement();
        $statement->date = $stmt->date;
        $statement->original_balance = $stmt->original_balance;
        $statement->new_balance = $stmt->new_balance;

        if ($stmt->account) {
            $statement->account = new \Codelicious\BelgianBankStatement\Data\Account();
            $statement->account->name = $stmt->account->name;
            $statement->account->number = $stmt->account->number;
            $statement->account->bic = $stmt->account->bic;
            $statement->account->country = $stmt->account->country;
            $statement->account->currency = $stmt->account->currency;
        }

        foreach($stmt->transactions as $tr) {
            $transaction = new \Codelicious\BelgianBankStatement\Data\Transaction();

            if ($tr->account) {
                $transaction->account = new \Codelicious\BelgianBankStatement\Data\Account();
                $transaction->account->name = $tr->account->name;
                $transaction->account->number = $tr->account->number;
                $transaction->account->bic = $tr->account->bic;
                $transaction->account->country = $tr->account->country;
                $transaction->account->currency = $tr->account->currency;
            }

            array_push($statement->transactions, $transaction);
        }

        return $statement;
    }
}