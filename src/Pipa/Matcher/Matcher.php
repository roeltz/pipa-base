<?php

namespace Pipa\Matcher;

class Matcher {
	
	protected $patterns;
	
	function __construct(array $patterns) {
		$this->patterns = $patterns;
	}
	
	function match(array $state, array &$extractedData) {
		foreach($this->patterns as $pattern) {
			if ($pattern->match($state, $extractedData)) {
				return $pattern->getValue();
			}
		}
		return false;
	}
}
