<?php

namespace Pipa\Annotation;
use Pipa\Parser\Match;
use Pipa\Parser\ProductionRule;
use Pipa\Parser\Symbol\Alternative;
use Pipa\Parser\Symbol\Literal;
use Pipa\Parser\Symbol\NonTerminal;
use Pipa\Parser\Symbol\Regex;

class AnnotationConstRule extends ProductionRule {
	
	function __construct() {
		parent::__construct(array(
			'const'=>new Alternative(array(
				'global'=>new Regex('[A-Z_]+'),
				'local'=>new Regex('::[A-Z_]+'),
				'scoped'=>new NonTerminal(array(
					'class'=>new AnnotationClassSymbol(),
					'separator'=>new Literal('::'),
					'name'=>new Regex('[A-Z_]+')
				))
			))
		));
	}
	
	function toNode(Match $match) {
		switch($type = $match->value['const']->name) {
			case 'global':
				$name = $match->value['const']->value;
				break;
			case 'local':
				$name = ltrim($match->value['const']->value, '::');
				break;
			case 'scoped':
				$name = $match->value['const']->value['class']->value.'::'.$match->value['const']->value['name']->value;
				break;
		}
		return new AnnotationConstNode($name, $type);
	}
}
