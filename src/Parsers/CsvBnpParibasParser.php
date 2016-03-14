<?php

namespace Codelicious\BelgianBankStatement\Parsers;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class CsvBnpParibasParser extends AbstractParser {

    public function parse($content)
    {
        $path = 'php://memory';
        $h = fopen($path, "rw+");
        fwrite($h, implode("\n", $content));
        fseek($h, 0);

        $statement = $this->parseFileHandle($h);

        fclose($h);

        return array($statement);
    }

    private function parseFileHandle($handle)
    {
        // credits: based on https://github.com/robarov/csv2mt940

        $statement = new \Codelicious\BelgianBankStatement\Data\Statement();
        $statement->account = new \Codelicious\BelgianBankStatement\Data\Account();

        while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {

            $transaction = new \Codelicious\BelgianBankStatement\Data\Transaction();
            $transaction->account = new \Codelicious\BelgianBankStatement\Data\Account();

            $transaction->message = $data[6];
            $transaction->transaction_date = $this->convert_date($data[1]);
            $transaction->valuta_date = $this->convert_date($data[2]);
            $transaction->amount = (float)str_replace(',', '.', $data[3]);
            $transaction->account->number = trim($data[5]);
            $transaction->account->currency = $data[4];
            $statement->account->number = trim($data[7]);

            array_push($statement->transactions, $transaction);
        }

        if ($statement->transactions)
            array_shift($statement->transactions); // We don't need the first row, as it contains the column headers

        return $statement;
    }

    private function convert_date($custom_date)
    {
        $date = $custom_date;
        if (strlen($custom_date) == 10) {
            $date = substr($custom_date, 6, 4) . "-" . substr($custom_date, 3, 2) . "-" . substr($custom_date, 0, 2);
        }

        return $date;
    }

}