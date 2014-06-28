<?php

namespace Pipa\Parser;

interface Symbol {
	function match($string, $start = 0);
}
