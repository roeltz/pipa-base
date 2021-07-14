<?php

namespace Pipa\Parser\Std\Rule;
use Pipa\Parser\SymbolMatch;
use Pipa\Parser\Symbol;
use Pipa\Parser\Symbol\Rule;
use Pipa\Parser\Symbol\NonTerminal;
use Pipa\Parser\Symbol\Quantified\ZeroOrMore;

class ItemList extends Rule {
	
	function __construct(Symbol $item, Symbol $separator) {
		parent::__construct(array(
			'item'=>$item,
			'next'=>new ZeroOrMore(new NonTerminal(array(
				'separator'=>$separator,
				'item'=>$item
			)))
		));
	}
	
	function process(array $matches) {
		$items = array($matches['item']);
		if (isset($matches['next'])) {
			foreach($matches['next']->value as $match) {
				$items[] = $match->value['item'];
			}
		}
		return $items;
	}
}
