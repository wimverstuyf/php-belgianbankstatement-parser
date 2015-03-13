<?php

spl_autoload_register(
    function ($class) {
        static $classes = NULL;
        static $path = NULL;

        if ($classes === NULL) {
            $classes = array(
                'Codelicious\\BelgianBankStatement\\Parsers\\AbstractParser' => '/Parsers/AbstractParser.php',
                'Codelicious\\BelgianBankStatement\\Parsers\\CodaParser' => '/Parsers/CodaParser.php',
                'Codelicious\\BelgianBankStatement\\Parsers\\Mt940Parser' => '/Parsers/Mt940Parser.php',
                'Codelicious\\BelgianBankStatement\\Parsers\\CsvBnpParibasParser' => '/Parsers/CsvBnpParibasParser.php',
                'Codelicious\\BelgianBankStatement\\Data\\Account' => '/Data/Account.php',
                'Codelicious\\BelgianBankStatement\\Data\\Statement' => '/Data/Statement.php',
                'Codelicious\\BelgianBankStatement\\Data\\Transaction' => '/Data/Transaction.php',
            );
            $path = dirname(__FILE__);
        }

        if (isset($classes[$class])) {
            require $path . $classes[$class];
        }
    }
);
