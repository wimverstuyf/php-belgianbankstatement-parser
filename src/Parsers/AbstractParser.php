<?php

namespace Codelicious\BelgianBankStatement\Parsers;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
abstract class AbstractParser {

    abstract public function parse($content);

    public function parseFile($file)
    {
        return $this->parse(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }

}