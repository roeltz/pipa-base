<?php

namespace Pipa\Dispatch;
use Pipa\Dispatch\Exception\RoutingException;
use Pipa\Match\Expression;
use Pipa\Match\Matcher;

class ExpressionRouter implements Router {

	protected $routes = array();
	protected $data = array();
	protected static $expressions = array();

	static function registerExpression(Expression $expression, $context) {
		self::$expressions[$context][] = $expression;
	}

	function __construct(array $routes = null) {
		if ($routes) {
			foreach($routes as $expression=>$callable) {
				$this->add($expression, $callable);
			}
		}
	}

	function add($expression, $callable, array $data = null) {
		$this->routes[$expression] = $callable;
		$this->data[$expression] = $data;
		return $this;
	}

	function resolve(Dispatch $dispatch) {
		$patterns = array();
		$expressionRules = self::$expressions[$dispatch->request->context];

		foreach($this->routes as $expression=>$callable) {
			foreach($expressionRules as $expressionRule) {
				if ($pattern = $expressionRule->getPattern($expression, $callable)) {
					$patterns[] = $pattern;
					break;
				}
			}
		}

		$matcher = new Matcher($patterns);
		$extractedData = array();
		if ($callable = $matcher->match($dispatch->getComparableState(), $extractedData)) {
			if (($index = array_search($callable, $this->routes)) && ($data = $this->data[$index])) {
				$extractedData = array_merge($extractedData, $data);
			}

			if (is_string($callable)) {
				@list($callable, $dataOverride) = explode("?", $callable);
				if ($dataOverride) {
					parse_str($dataOverride, $dataOverride);
					$extractedData = array_merge($extractedData, $dataOverride);
				}
			}

			foreach($extractedData as $k=>$v) {
				$dispatch->request->data[$k] = $v;
			}
			return new Action($callable);
		} else {
			throw new RoutingException("Action not resolved");
		}
	}
}
