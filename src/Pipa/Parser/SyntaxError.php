<?php

namespace Pipa\Parser;
use Exception;
use Pipa\Parser\Symbol\Rule;

class SyntaxError extends Exception {
	
	private $offset;
	private $string;
	private $rule;
		
	function __construct($string, $offset, Rule $rule = null) {
		$this->string = $string;
		$this->offset = $offset;
		$this->rule = $rule;
		list($line, $col) = $this->getLocation($string, $offset);
		parent::__construct("Syntax error at $line:$col, at '".substr($string, $offset, 20)."...'");
	}
	
	function getLocation($string, $offset) {
		$line = substr_count($string, "\n", 0, $offset);
		$col = $offset - strrpos(substr($string, 0, $offset), "\n");
		return array($line + 1, $col);
	}
	
	function getOffset() {
		return $this->offset;
	}

	function getRule() {
		return $this->rule;
	}

	function getString() {
		return $this->string;
	}
}
