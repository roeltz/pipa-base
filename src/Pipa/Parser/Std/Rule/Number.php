<?php

namespace Pipa\Parser\Std\Rule;
use Pipa\Parser\Match;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\Symbol\Regex;

class Number extends ProductionRule {
	
	function __construct() {
		parent::__construct(array(
			'number'=>new Regex('-?[0-9]*\.?[0-9]+'),
		));
	}
	
	function toNode(Match $match) {
		return (double) $match->value['number']->value;
	}
}
