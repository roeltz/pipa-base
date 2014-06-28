<?php

namespace Pipa\Parser\Symbol;
use Pipa\Parser\Match;
use Pipa\Parser\SyntaxError;

abstract class Rule extends NonTerminal {
	
	function match($string, $start = 0) {
		$matches = array();
		$length = 0;
		if ($this->debug) $this->debug->log($this, $string, $start)->down();
		
		foreach($this->symbols as $name=>$symbol) {
			if ($this->debug) $this->debug->log($symbol, $string, $start, $name);
			
			if ($match = $symbol->match($string, $start + $length)) {
				if ($this->debug) $this->debug->logMatch($match, $string);
				$matches[$name] = $match;
				$length += $match->length;
			} elseif ($length) {
				throw new SyntaxError($string, $start + $length, $this);
			} else {
				if ($this->debug) $this->debug->backtrack()->up();
				return;
			}
		}
		
		if ($this->debug) $this->debug->up();
		return new Match($this, $start, $length, $this->process($matches));
	}
}
