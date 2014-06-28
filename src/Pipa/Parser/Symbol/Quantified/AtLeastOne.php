<?php

namespace Pipa\Parser\Symbol\Quantified;
use Pipa\Parser\Symbol\Quantified;

class AtLeastOne extends Quantified {
		
	function validate($clicks) {
		return $clicks >= 1; 
	}
}
