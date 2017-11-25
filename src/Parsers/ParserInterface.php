<?php

namespace Codelicious\BelgianBankStatement\Parsers;

use Codelicious\BelgianBankStatement\Values\Statement;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
interface ParserInterface
{
	/**
	 * @param string $contentToParse
	 * @return Statement[]
	 */
	function parse(string $contentToParse): array;
}