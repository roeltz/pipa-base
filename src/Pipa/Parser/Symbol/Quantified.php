<?php

namespace Pipa\Parser\Symbol;
use Pipa\Parser\Debug;
use Pipa\Parser\Debuggable;
use Pipa\Parser\SymbolMatch;
use Pipa\Parser\Symbol;

abstract class Quantified implements Symbol, Debuggable {
	
	protected $debug;
	protected $symbol;
	
	abstract function validate($clicks);
	
	function __construct(Symbol $symbol) {
		$this->symbol = $symbol;
	}
	
	function match($string, $start = 0) {
		$length = 0;
		$matches = array();
		$clicks = 0;
		
		if ($this->debug) $this->debug->log($this, $string, $start)->down();
		
		while ($match = $this->symbol->match($string, $start + $length)) {
			if ($this->debug) $this->debug->logMatch($match, $string);
			$length += $match->length;
			$matches[] = $match;
			$clicks++;
			
			if (!$match->length)
				break;
		}
		
		
		
		if ($this->validate($clicks)) {
			if ($this->debug) $this->debug->up();
			return new SymbolMatch($this, $start, $length, $this->process($matches));
		} else {
			if ($this->debug) $this->debug->backtrack()->up();
		}
	}
	
	function process(array $matches) {
		return $matches;
	}
	
	function getSymbols() {
		return array($this->symbol);
	}
	
	function setDebug(Debug $debug) {
		$this->debug = $debug;
	}	

}
