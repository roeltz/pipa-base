<?php

namespace Pipa\Parser\Symbol\Quantified;
use Pipa\Parser\Symbol;
use Pipa\Parser\Symbol\Quantified;

class Interval extends Quantified {
	
	protected $max;
	protected $min;
	
	function __construct($min, $max, Symbol $symbol) {
		parent::__construct($symbol);
		$this->min = $min;
		$this->max = $max;
	}
		
	function validate($clicks) {
		return $clicks >= $this->min && $clicks <= $this->max;
	}
}
