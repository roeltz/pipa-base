<?php

namespace Pipa\Parser\Symbol;

class WhitespacedLiteral extends Regex {
	
	function __construct($token) {
		parent::__construct('\s*'.preg_quote($token).'\s*');
	}
}
