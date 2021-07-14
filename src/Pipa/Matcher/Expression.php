<?php

namespace Pipa\Matcher;
use Pipa\Parser\SymbolMatch;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\SyntaxError;

abstract class Expression extends ProductionRule {

	abstract function toPattern(SymbolMatch $match);
	
	function getPattern($expression, $value) {
		try {
			if ($match = $this->match($expression)) {
				$pattern = $this->toNode($match);
				$pattern->setValue($value);
				return $pattern;
			}
		} catch(SyntaxError $e) {
			return false;
		}
	}
	
	function toNode(SymbolMatch $match) {
		return $this->toPattern($match);
	}
}
