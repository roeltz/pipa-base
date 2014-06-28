<?php

namespace Pipa\Parser\Symbol;

class Literal extends Regex {
	
	function __construct($token) {
		parent::__construct(preg_quote($token));
	}
}
