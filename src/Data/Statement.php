<?php

namespace Codelicious\BelgianBankStatement\Data;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Statement {
    public $date;
    public $account;
    public $original_balance;
    public $new_balance;

    public $transactions = array();
}
