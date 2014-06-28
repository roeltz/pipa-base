<?php

namespace Pipa\Annotation;
use Pipa\Parser\Match;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\Std\Rule\Boolean;
use Pipa\Parser\Std\Rule\Number;
use Pipa\Parser\Std\Rule\String;
use Pipa\Parser\Symbol\Alternative;
use Pipa\Parser\Symbol\Literal;
use Pipa\Parser\Symbol\NonTerminal;

class AnnotationValueSymbol extends NonTerminal {
	
	function __construct() {
		parent::__construct(array(
			'value'=>new Alternative(array(
				'string'=>new String(),
				'number'=>new Number(),
				'boolean'=>new Boolean(),
				'null'=>new Literal('null'),
				'const'=>new AnnotationConstRule()
			))
		));
	}
	
	function process(array $matches) {
		if ($matches['value']->symbol instanceof ProductionRule) {
			return $matches['value']->symbol->toNode($matches['value']);
		}
	}
}