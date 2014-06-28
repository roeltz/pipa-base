<?php

namespace Pipa\Annotation;

class AnnotationNode {
	
	public $class;
	public $values;
	
	function __construct($class, array $values = null) {
		$this->class = $class;
		$this->values = $values;
	}
}
