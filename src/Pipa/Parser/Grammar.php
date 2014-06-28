<?php

namespace Pipa\Parser;

class Grammar {
		
	protected $rules;
	
	function __construct(array $rules) {
		$this->rules = $rules;
	}
	
	function addRule($id, ProductionRule $rule) {
		$this->rules[$id] = $rule;
	}
	
	function getRules() {
		return $this->rules;
	}
}
