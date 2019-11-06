<?php

namespace Pipa\Annotation;
use Pipa\Parser\Match;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\Symbol\Literal;
use Pipa\Parser\Symbol\WhitespacedLiteral;
use Pipa\Parser\Std\Rule\ItemList;

class AnnotationArrayRule extends ProductionRule {
	
	function __construct(AnnotationArrayValueSymbol $arrayValue) {
		parent::__construct(array(
			'opening'=>new WhitespacedLiteral('{'),
			'values'=>new ItemList($arrayValue, new WhitespacedLiteral(',')),
			'closing'=>new WhitespacedLiteral('}')
		));
	}
	
	function toNode(Match $match) {
		$values = array();
		foreach($match->value['values']->value as $match) {
			if (is_array($value = $match->value['value']->value)) {
				$keys = array_keys($value);
				$values[$keys[0]] = $value[$keys[0]];
			} else {
				$values[] = $value;
			}
		}
		return $values;
	}
}
