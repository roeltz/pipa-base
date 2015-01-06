<?php

namespace Pipa\Dispatch;
use Pipa\Match\HasComparableState;
use ReflectionFunctionAbstract;

class Result implements HasComparableState {
	
	public $data;
	public $outOfBand;
	public $options;
	
	static function from($result, array $options = array()) {
		$result = $result instanceof self ? $result : new self($result);
		$result->options = array_merge($options, $result->options);
		return $result;
	}
	
	function __construct($data, array $options = array(), array $outOfBand = array()) {
		$this->data = $data;
		$this->options = $options;
		$this->outOfBand = $outOfBand;
	}
	
	function getComparableState() {
		$state = array();
		foreach($this->options as $name=>$value) {
			$state["option:$name"] = $value;
		}
		return $state;
	}
}
