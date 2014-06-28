<?php

namespace Pipa\Parser;
use Pipa\Parser\Std\Symbol\Whitespace;

class Parser {
	
	protected $grammar;
	protected $recover;
	
	function __construct(Grammar $grammar, $recover = false) {
		$this->grammar = $grammar;
		$this->recover = $recover;
	}
	
	function parse($string) {
		$nodes = array();
		$cursor = 0;
		$length = strlen($string);
		$whitespace = new Whitespace();
		
		while ($cursor < $length) {
			if ($match = $whitespace->match($string, $cursor)) {
				$cursor += $match->length;
			} else {
				foreach($this->grammar->getRules() as $rule) {
					try {
						if ($match = $rule->match($string, $cursor)) {
							$cursor += $match->length;
							if ($rule instanceof ProductionRule) {
								$nodes[] = $rule->toNode($match);
							}
							break;
						}
					} catch(SyntaxError $e) {
						if ($this->recover) {
							$cursor = $e->getOffset();
						} else {
							throw $e;
						}
					}
				}
				if (!$match) {
					if ($this->recover) {
						$cursor++;
					} else {
						throw new SyntaxError($string, $cursor);
					}
				}
			}
		}
		
		return $nodes;
	}
}
