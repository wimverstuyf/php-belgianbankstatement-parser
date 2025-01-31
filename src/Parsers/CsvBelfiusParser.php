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

	/**
	 * Converts a date string to YYYY-MM-DD format
	 * 
	 * @param string $dateString The date string to convert (accepts DD/MM/YYYY or DD/MM/YY format)
	 * @return DateTime Returns a DateTime object representing the parsed date
	 */
    private function convertDate($dateString): DateTime
    {
        $date = $dateString;
        if (mb_strlen($dateString) == 10) {
            $date = substr($dateString, 6, 4) . "-" . substr($dateString, 3, 2) . "-" . substr($dateString, 0, 2);
        } elseif (mb_strlen($dateString) == 8) {
            $date = "20" . substr($dateString, 6, 2) . "-" . substr($dateString, 3, 2) . "-" . substr($dateString, 0, 2);
        }

        return new DateTime($date);
    }

	/**
	 * Returns the CSV separator character used in Belfius bank statements
	 * 
	 * @return string The separator character (semicolon)
	 */
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
        $headers = ['Boekingsdatum vanaf', 'Boekingsdatum tot en met', 'Bedrag vanaf', 'Bedrag tot en met', 'Rekeninguittrekselnummer vanaf', 'Rekeninguittrekselnummer tot en met', 'Afschriftnummer vanaf', 'Afschriftnummer tot en met', 'Mededeling', 'Naam tegenpartij bevat', 'Rekening tegenpartij', 'Laatste saldo', 'Datum/uur van het laatste saldo', 'Rekening'];

        if (in_array(trim($data[0]), $headers) || (trim($data[0]) == '' && count($data) < 3)) {
            return [null, null];
        }

        if (count($data) < 14) {
            throw new UnexpectedValueException("CSV content invalid");
        }

        $account = new Account(
            "",                 // accountNumber
            "",                 // bic
            trim($data[0]),     // name
            "",                 // currency
            ""                  // countryCode
        );

        return [$account, new Transaction(
            new Account(
                trim($data[5]),     // accountNumber
                trim($data[12]),    // bic
                trim($data[4]),     // name
                (string)$data[11],  // currency
                trim($data[13])     // countryCode
            ),
            trim($data[8]),                                // statementLine
            $this->convertDate((string)$data[1]),          // transactionDate
            $this->convertDate((string)$data[9]),          // valueDate
            (float)str_replace(',', '.', $data[10]),       // amount
            trim($data[14]),                               // message
            ''                                             // extraDetails
        )];
    }
}
