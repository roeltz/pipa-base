<?php

namespace Pipa\Annotation;
use Pipa\Parser\Match;
use Pipa\Parser\Symbol\Regex;
use Pipa\Parser\Symbol\WhitespacedLiteral;
use Pipa\Parser\Symbol\NonTerminal;

class AnnotationParameterSymbol extends NonTerminal {
		
	function __construct(AnnotationValueSymbol $value) {
		parent::__construct(array(
			'identifier'=>new Regex('\\w+'),
			'equals'=>new WhitespacedLiteral('='),
			'value'=>$value
		));
	}
	
	function process(array $matches) {
		return array(
			$matches['identifier']->value=>$matches['value']->value
		);
	}
}