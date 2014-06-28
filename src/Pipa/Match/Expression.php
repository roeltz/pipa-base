<?php

namespace Pipa\Match;
use Pipa\Parser\Match;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\SyntaxError;

abstract class Expression extends ProductionRule {

	abstract function toPattern(Match $match);
	
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
	
	function toNode(Match $match) {
		return $this->toPattern($match);
	}
}
