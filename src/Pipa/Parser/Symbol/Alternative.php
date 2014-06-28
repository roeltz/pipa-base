<?php

namespace Pipa\Parser\Symbol;

class Alternative extends NonTerminal {
		
	function match($string, $start = 0) {
		if ($this->debug) $this->debug->log($this, $string, $start)->down();
		
		foreach($this->symbols as $name=>$symbol) {
			if ($this->debug) $this->debug->log($symbol, $string, $start, $name);
			
			if ($match = $symbol->match($string, $start)) {
				if ($this->debug) $this->debug->logMatch($match, $string);
				$match->name = $name;
				if ($this->debug) $this->debug->up();
				return $match;
			}
		}
		
		if ($this->debug) $this->debug->backtrack()->up();
	}
} 