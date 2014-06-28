<?php

namespace Pipa\Annotation;

class AnnotationConstNode {
	
	const TYPE_GLOBAL = 'global';
	const TYPE_LOCAL = 'local';
	const TYPE_SCOPED = 'scoped';
	
	public $name;
	public $type;
	
	function __construct($name, $type) {
		$this->name = $name;
		$this->type = $type;
	}
}
