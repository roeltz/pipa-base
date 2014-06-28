<?php

namespace Pipa\Parser\Symbol;
use Pipa\Parser\Debug;
use Pipa\Parser\Debuggable;
use Pipa\Parser\Match;
use Pipa\Parser\Symbol;

class NonTerminal implements Symbol, Debuggable {
	
	protected $debug;
	protected $symbols;
	
	function __construct(array $symbols) {
		$this->symbols = $symbols;
	}
	
	function addSymbol($name, Symbol $symbol) {
		$this->symbols[$name] = $symbol;
	}
	
	function getSymbol($name) {
		return $this->symbols[$name];
	}
	
	function match($string, $start = 0) {
		$matches = array();
		$length = 0;
		if ($this->debug) $this->debug->log($this, $string, $start)->down();
		
		foreach($this->symbols as $name=>$symbol) {
			if ($this->debug) $this->debug->log($symbol, $string, $start, $name);
			
			if ($match = $symbol->match($string, $start + $length)) {
				if ($this->debug) $this->debug->logMatch($match, $string);
				if (!$match->name) $match->name = $name; 
				$matches[$name] = $match;
				$length += $match->length;
			} else {
				if ($this->debug) $this->debug->backtrack()->up();
				return;
			}
		}
		
		if ($this->debug) $this->debug->up();
		
		return new Match($this, $start, $length, $this->process($matches));
	}
	
	function process(array $matches) {
		return $matches;
	}

	function getSymbols() {
		return $this->symbols;
	}
	
	function setDebug(Debug $debug) {
		$this->debug = $debug;
	}	
}
