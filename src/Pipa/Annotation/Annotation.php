<?php

namespace Pipa\Annotation;
use Reflector;

class Annotation {
	
	public $value;
	
	function __construct(array $values = array()) {
		$properties = get_object_vars($this);
		foreach($values as $property=>$value) {
			if (array_key_exists($property, $properties)) {
				$this->$property = $value;
			} else {
				throw new \Exception("Invalid property '$property' for annotation class '".get_called_class()."'");
			}
		}
	}
	
	function check($target = null) {}
}
