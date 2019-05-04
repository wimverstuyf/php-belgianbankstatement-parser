<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Account;
use Codelicious\BelgianBankStatement\Values\Transaction;
use DateTime;
use UnexpectedValueException;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class CsvBelfiusParser extends CsvParser {

    private function convertDate($dateString): DateTime
    {
        $date = $dateString;
        if (mb_strlen($dateString) == 10) {
            $date = substr($dateString, 6, 4) . "-" . substr($dateString, 3, 2) . "-" . substr($dateString, 0, 2);
        }

        return new DateTime($date);
    }

    protected function getSeparator(): string
    {
        return ";";
    }

    /**
     * @param array $data
     * @return array [Account, Transaction]
     */
    protected function parseLine(array $data): array
    {
        $headers = ['Boekingsdatum vanaf', 'Boekingsdatum tot en met', 'Bedrag vanaf', 'Bedrag tot en met', 'Afschriftnummer vanaf', 'Afschriftnummer tot en met', 'Mededeling', 'Naam tegenpartij bevat', 'Rekening tegenpartij', 'Laatste saldo', 'Datum/uur van het laatste saldo', 'Rekening'];

        if (in_array($data[0], $headers) || ($data[0] == '' && count($data) < 3)) {
            return [null, null];
        }

        if (count($data) < 14) {
            throw new UnexpectedValueException("CSV content invalid");
        }

        $account = new Account("", "", trim($data[0]), "", "");

        return [$account, new Transaction(
            new Account(
                trim($data[5]),
                trim($data[12]),
                trim($data[4]),
                (string)$data[11],
                trim($data[13])
            ),
            trim($data[8]),
            $this->convertDate((string)$data[1]),
            $this->convertDate((string)$data[9]),
            (float)str_replace(',', '.', $data[10]),
            trim($data[14]),
            ''
        )];
    }
}
