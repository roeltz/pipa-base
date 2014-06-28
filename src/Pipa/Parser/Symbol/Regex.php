<?php

namespace Pipa\Parser\Symbol;
use Pipa\Parser\Match;
use Pipa\Parser\Symbol;

class Regex implements Symbol {
	
	protected $debug;
	protected $regex;
	
	function __construct($regex, $modifiers = "") {
		$this->regex = "/^$regex/$modifiers";
	}
	
	function match($string, $start = 0) {
		if (preg_match($this->regex, substr($string, $start), $m)) {
			return new Match($this, $start, strlen($m[0]), $m[0]);
		}
	}
}
