<?php

namespace Pipa\Annotation;
use Pipa\Parser\Grammar;
use Pipa\Parser\Symbol\Regex;

class AnnotationGrammar extends Grammar {
	
	function __construct() {
		parent::__construct(array(
			'non-annotation'=>new Regex('[^@]+'),
			'annotation'=>new AnnotationRule()
		));
	}
}
