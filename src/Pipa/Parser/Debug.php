<?php

namespace Pipa\Parser;

class Debug {
	
	protected $length;
	protected $depth = 1;
	
	function __construct($length = 40) {
		$this->length = $length;
	}
	
	function apply(Debuggable $symbol, array &$visited = null) {
		if (!$visited) $visited = array();
		if (in_array($symbol, $visited, true)) return; else $visited[] = $symbol;
		
		$symbol->setDebug($this);
		foreach($symbol->getSymbols() as $s) {
			if ($s instanceof Debuggable) {
				$this->apply($s, $visited);
			}
		}
		return $this;
	}
	
	function backtrack() {
		echo "BKTRK: ".str_repeat(" ", $this->depth * 2)."[X]\n";
		return $this;
	}
	
	function down() {
		$this->depth++;
		return $this;
	}
	
	function log(Symbol $symbol, $string, $offset, $name = null) {
		echo "DEBUG: ".str_repeat(" ", $this->depth * 2).str_pad("'".substr($string, $offset, $this->length)."'", $this->length + 2, ".", STR_PAD_RIGHT)."(".get_class($symbol);
		if ($name) echo "#$name";
		echo ")\n";
		return $this;
	}
	
	function logMatch(Match $match, $string) {
		echo "MATCH: ".str_repeat(" ", $this->depth * 2)."'".substr($string, $match->start, $match->length)."'\n";
		return $this;
	}
	
	function up() {
		$this->depth--;
		return $this;
	}
}
