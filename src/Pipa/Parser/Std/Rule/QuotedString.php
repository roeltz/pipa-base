<?php

namespace Pipa\Parser\Std\Rule;
use Pipa\Parser\SymbolMatch;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\Symbol\Regex;
use Pipa\Parser\Symbol\Literal;

class QuotedString extends ProductionRule {
	
	function __construct() {
		parent::__construct(array(
			'opening'=>new Literal('"'),
			'content'=>new Regex('([^"\\\\]|\\\\.)*'),
			'closing'=>new Literal('"')
		));
	}
	
	function toNode(SymbolMatch $match) {
		return json_decode('"'.$match->value['content']->value.'"');
	}
}
