<?php

namespace Pipa\Dispatch;
use Pipa\Matcher\HasComparableState;

abstract class Request implements HasComparableState {
	
	public $context;
	public $data;
	
	function __construct($context, array $data = array()) {
		$this->context = $context;
		$this->data = $data;
	}
}
