<?php

namespace Pipa\Parser;
use Pipa\Parser\Symbol\Rule;

abstract class ProductionRule extends Rule {
	abstract function toNode(SymbolMatch $match);
}
