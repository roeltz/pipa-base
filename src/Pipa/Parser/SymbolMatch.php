<?php

namespace Pipa\Parser;

class SymbolMatch {
	public $symbol;
	public $start;
	public $length;
	public $value;
	public $name;
	
	function __construct(Symbol $symbol, $start, $length, $value, $name = null) {
		$this->symbol = $symbol;
		$this->start = $start;
		$this->length = $length;
		$this->value = $value;
		$this->name = $name;
	}
}
