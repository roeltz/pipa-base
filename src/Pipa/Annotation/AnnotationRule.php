<?php

namespace Pipa\Annotation;
use Pipa\Parser\Debug;
use Pipa\Parser\Match;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\Std\Rule\Boolean;
use Pipa\Parser\Std\Rule\ItemList;
use Pipa\Parser\Std\Rule\Number;
use Pipa\Parser\Symbol\Alternative;
use Pipa\Parser\Symbol\Quantified\ZeroOrMore;
use Pipa\Parser\Symbol\Quantified\ZeroOrOne;
use Pipa\Parser\Symbol\NonTerminal;
use Pipa\Parser\Symbol\Literal;
use Pipa\Parser\Symbol\Regex;
use Pipa\Parser\Symbol\WhitespacedLiteral;

class AnnotationRule extends ProductionRule {

	function __construct() {
		$value = new AnnotationValueSymbol();
		$parameter = new AnnotationParameterSymbol($value);
		$arrayValue = new AnnotationArrayValueSymbol($value, $parameter);
		$array = new AnnotationArrayRule($arrayValue);
		$value->getSymbol('value')->addSymbol('array', $array);
		$value->getSymbol('value')->addSymbol('annotation', $this);

		parent::__construct(array(
			'sigil'=>new Literal('@'),
			'class'=>new AnnotationClassSymbol(),
			'parameters'=>new ZeroOrOne(new NonTerminal(array(
				'opening'=>new Regex('\\(\\s*'),
				'parameter'=>new Alternative(array(
					'single'=>$value,
					'list'=>new ItemList($parameter, new WhitespacedLiteral(','))
				)),
				'closing'=>new WhitespacedLiteral(')')
			)))
		));
	}

	function toNode(Match $match) {
		$class = $match->value['class']->value;

		if ($parameter = @$match->value['parameters']->value->value['parameter']) {
			$values = array();
			if ($parameter->name == 'single') {
				$values['value'] = $parameter->value;
			} else {
				foreach($parameter->value as $p) {
					foreach ($p->value as $k=>$v) {
						$values[$k] = $v;
						break;
					}
				}
			}
			return new AnnotationNode($class, $values);
		} else {
			return new AnnotationNode($class);
		}
	}
}
