<?php

namespace Pipa\Parser\Symbol\Quantified;
use Pipa\Parser\Symbol\Quantified;

class ZeroOrMore extends Quantified {
		
	function validate($clicks) {
		return true; 
	}
}
