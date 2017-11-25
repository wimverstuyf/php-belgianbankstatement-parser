<?php

namespace Codelicious\BelgianBankStatement\Values;

use DateTime;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Transaction {
	
	/** @var Account */
	private $account;
	/** @var DateTime */
	private $transactionDate;
	/** @var DateTime */
	private $valutaDate;
	/** @var float */
	private $amount;
	/** @var string */
	private $message;
	/** @var string */
	private $structuredMessage;
	
	public function __construct(Account $account, DateTime $transactionDate, DateTime $valutaDate, float $amount, string $message, string $structuredMessage)
	{
		$this->account = $account;
		$this->transactionDate = $transactionDate;
		$this->valutaDate = $valutaDate;
		$this->amount = $amount;
		$this->message = $message;
		$this->structuredMessage = $structuredMessage;
	}
	
	public function getAccount(): Account
	{
		return $this->account;
	}
	
	public function getTransactionDate(): DateTime
	{
		return $this->transactionDate;
	}
	
	public function getValutaDate(): DateTime
	{
		return $this->valutaDate;
	}
	
	public function getAmount(): float
	{
		return $this->amount;
	}
	
	public function getMessage(): string
	{
		return $this->message;
	}
	
	public function getStructuredMessage(): string
	{
		return $this->structuredMessage;
	}
}