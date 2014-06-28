<?php

namespace Pipa\Annotation;
use Pipa\Parser\Symbol\Regex;

class AnnotationClassSymbol extends Regex {
	
	function __construct() {
		parent::__construct('((\\w+\\\\)*)?[A-Z]\\w*');
	}
}