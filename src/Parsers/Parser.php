<?php

namespace Codelicious\BelgianBankStatement\Parsers;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Parser {

    /**
     * @param $content
     * @param $type
     * @return \Codelicious\BelgianBankStatement\Data\Statement|null
     */
    public function parse($content, $type)
    {
        $parser = NULL;

        switch($type)
        {
            case "csv":
            case "csv_bnpparibas":
                $parser = new CsvBnpParibasParser();
                break;
            case "coda":
                $parser = new CodaParser();
                break;
            case "mt940":
                $parser = new Mt940Parser();
                break;
            default:
                return NULL;
        }

        return $parser->parse($content);
    }

    public function parseFile($file, $type)
    {
        return $this->parse(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), $type);
    }
}
