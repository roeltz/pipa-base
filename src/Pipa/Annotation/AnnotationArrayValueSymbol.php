<?php

namespace Pipa\Annotation;
use Pipa\Parser\SymbolMatch;
use Pipa\Parser\Symbol\Alternative;
use Pipa\Parser\Symbol\NonTerminal;

class AnnotationArrayValueSymbol extends NonTerminal {
	
	function __construct(AnnotationValueSymbol $value, AnnotationParameterSymbol $parameter) {
		parent::__construct(array(
			'value'=>new Alternative(array(
				'numeric'=>$value,
				'associative'=>$parameter
			))
		));
	}

	function toNode(SymbolMatch $match) {
		return array(
			$match->value['value']->name=>$match->value['value']->value
		);
	}
}