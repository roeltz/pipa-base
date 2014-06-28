<?php

namespace Pipa\Parser\Std\Symbol;
use Pipa\Parser\Symbol\Regex;

class Whitespace extends Regex {
	
	const PATTERN_MULTILINE = '[\\s\\r\\n]+';
	const PATTERN_SIMPLE = '\\s+';
	
	function __construct($multiline = true) {
		parent::__construct($multiline ? self::PATTERN_MULTILINE : self::PATTERN_SIMPLE);
	}
}
