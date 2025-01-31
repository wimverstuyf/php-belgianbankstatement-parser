<?php

namespace Codelicious\BelgianBankStatement;

use Codelicious\BelgianBankStatement\Parsers\CodaParser;
use Codelicious\BelgianBankStatement\Parsers\CsvBnpParibasParser;
use Codelicious\BelgianBankStatement\Parsers\CsvBelfiusParser;
use Codelicious\BelgianBankStatement\Parsers\CsvKbcParser;
use Codelicious\BelgianBankStatement\Parsers\CsvIngParser;
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
	 * @param string $type csv_bnpparisbas or csv_kbc or coda or mt940
	 * @return Statement[]
	 */
	public function parse(string $content, string $type): array
	{
		return $this->getParser($type)->parse($content);
	}

	/**
	 * @param string $file filepath of the file to parse
	 * @param string $type csv_bnpparisbas or csv_kbc or coda or mt940
	 * @return Statement[]
	 */
	public function parseFile(string $file, string $type): array
	{
		return $this->getParser($type)->parseFile($file);
	}

	private function getParser(string $type): ParserInterface
	{
		/** @var ParserInterface|null $parser */
		$parser = null;

		switch($type)
		{
			case "csv": // option "csv" deprecated
			case "csv_bnpparibas":
				$parser = new CsvBnpParibasParser();
				break;
			case "csv_belfius":
				$parser = new CsvBelfiusParser();
				break;
			case "csv_kbc":
				$parser = new CsvKbcParser();
				break;
			case "csv_ing":
				$parser = new CsvIngParser();
				break;
			case "csv_triodos":
				$parser = new CsvTriodosParser();
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

		return $parser;
	}
}
