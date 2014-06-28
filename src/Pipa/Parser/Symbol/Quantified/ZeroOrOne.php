<?php

namespace Pipa\Parser\Symbol\Quantified;
use Pipa\Parser\Symbol\Quantified;

class ZeroOrOne extends Quantified {

	function process(array $matches) {
		return @$matches[0];
	}		
	
	function validate($clicks) {
		return ($clicks === 0) || ($clicks == 1); 
	}
}
