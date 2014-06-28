<?php

namespace Pipa\Match;

class Pattern {

	const VAR_PATTERN = '/\{[^}]+\}/';

	protected $state;
	protected $value;
	protected $compilations = array();

	function __construct(array $state) {
		$this->state = $state;
	}

	function getCapturingElements($property, $pattern) {
		if (!isset($this->compilations[$property])) {
			$vars = array();
			$regex = $pattern;
			$regex = preg_replace_callback(self::VAR_PATTERN, function($m) use(&$vars){
				@list($var, $regex) = explode(":", trim($m[0], '{}'), 2);
				$vars[] = $var;
				return $regex ? $regex : '([\w.-]+)';
			}, $regex);
			$regex = "#^{$regex}$#";
			$this->compilations[$property] = array($regex, $vars);
		}
		return $this->compilations[$property];
	}

	function getValue() {
		return $this->value;
	}

	function match($state, array &$extractedData) {
		$preliminarData = array();

		foreach($this->state as $property=>$value) {
			if (is_array($value)) {
				if (isset($value['regex'])) {
					if (!preg_match($value['regex'], @$state[$property]))
						return false;
				} elseif (isset($value['capture'])) {
					list($regex, $vars) = $this->getCapturingElements($property, $value['capture']);
					if (preg_match($regex, @$state[$property], $m)) {
						$slice = array_slice($m, 1);
						if ($slice) {
							$data = array_combine($vars, $slice);
							foreach($data as $k=>$v) {
								$preliminarData[$k] = $v;
							}
						}
					} else {
						return false;
					}
				} elseif (!isset($value['any'])) {
					return false;
				}
			} elseif ($value !== @$state[$property]) {
				return false;
			}
		}

		foreach($preliminarData as $k=>$v) {
			$extractedData[$k] = $v;
		}

		return true;
	}

	function setValue($value) {
		$this->value = $value;
	}
}
