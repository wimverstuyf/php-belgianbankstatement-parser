<?php

namespace Codelicious\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CodaParser;
use Codelicious\BelgianBankStatement\Parsers\CsvBnpParibasParser;
use Codelicious\BelgianBankStatement\Parsers\Mt940Parser;
use Codelicious\BelgianBankStatement\Parsers\ParserInterface;
use Codelicious\BelgianBankStatement\Values\Statement;
use InvalidArgumentException;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Parser {
	
	/**
	 * @param string $content content to parse
	 * @param string $type csv or coda or mt940
	 * @return Statement[]
	 */
    public function parse(string $content, string $type): array
    {
    	/** @var ParserInterface|null $parser */
        $parser = null;

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
                throw new InvalidArgumentException("type '$type' not valid");
        }

        return $parser->parse($content);
    }
	
	/**
	 * @param string $file filepath of the file to parse
	 * @param string $type csv or coda or mt940
	 * @return Statement
	 */
	public function parseFile(string $file, string $type): Statement
    {
        return $this->parse(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), $type);
    }
}
