<?php

namespace Codelicious\BelgianBankStatement\Values;

use DateTime;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Statement {
	
	/** @var DateTime */
	private $date;
	/** @var Account */
	private $account;
	/** @var float */
	private $initialBalance;
	/** @var float */
	private $newBalance;
	/** @var array */
	private $transactions;
	
	/**
	 * @param DateTime $date
	 * @param Account $account
	 * @param float $initialBalance
	 * @param float $newBalance
	 * @param Transaction[] $transactions
	 */
	public function __construct(DateTime $date, Account $account, float $initialBalance, float $newBalance, array $transactions)
	{
		$this->date = $date;
		$this->account = $account;
		$this->initialBalance = $initialBalance;
		$this->newBalance = $newBalance;
		$this->transactions = $transactions;
	}
	
	public function getDate(): DateTime
	{
		return $this->date;
	}
	
	public function getAccount(): Account
	{
		return $this->account;
	}
	
	public function getInitialBalance(): float
	{
		return $this->initialBalance;
	}
	
	public function getNewBalance(): float
	{
		return $this->newBalance;
	}
	
	/**
	 * @return Transaction[]
	 */
	public function getTransactions(): array
	{
		return $this->transactions;
	}
}
