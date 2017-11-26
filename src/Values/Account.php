<?php

namespace Codelicious\BelgianBankStatement\Values;

/**
 * @package Codelicious\BelgianBankStatement
 * @author Wim Verstuyf (wim.verstuyf@codelicious.be)
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Account {
	
	/** @var string */
	private $name;
	/** @var string */
	private $bic;
	/** @var string */
	private $number;
	/** @var string */
	private $currencyCode;
	/** @var string */
	private $countryCode;
	
	public function __construct(string $name, string $bic, string $number, string $currencyCode, string $countryCode)
	{
		$this->name = $name;
		$this->bic = $bic;
		$this->number = $number;
		$this->currencyCode = $currencyCode;
		$this->countryCode = $countryCode;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getBic(): string
	{
		return $this->bic;
	}
	
	public function getNumber(): string
	{
		return $this->number;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currencyCode;
	}
	
	public function getCountryCode(): string
	{
		return $this->countryCode;
	}
}