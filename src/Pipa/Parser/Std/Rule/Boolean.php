<?php

namespace Pipa\Parser\Std\Rule;
use Pipa\Parser\SymbolMatch;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\Symbol\Literal;
use Pipa\Parser\Symbol\Regex;

class Boolean extends ProductionRule {
	
	function __construct() {
		parent::__construct(array(
			'keyword'=>new Regex('true|false'),
		));
	}

	function toNode(SymbolMatch $match) {
		return $match->value['keyword']->value == "true";
	}
}
