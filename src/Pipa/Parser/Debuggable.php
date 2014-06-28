<?php

namespace Pipa\Parser;

interface Debuggable {
	function setDebug(Debug $debug);
	function getSymbols();
}
