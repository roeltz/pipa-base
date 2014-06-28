<?php

namespace Pipa\Dispatch;

class ActionRouter implements Router {
	
	protected $callable;

	function __construct($callable) {
		$this->callable = $callable;
	}
	
	function resolve(Dispatch $dispatch) {
		return new Action($this->callable);
	}
}
