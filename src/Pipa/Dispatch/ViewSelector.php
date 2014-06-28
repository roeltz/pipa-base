<?php

namespace Pipa\Dispatch;
use Pipa\Match\Expression;
use Pipa\Match\Matcher;

class ViewSelector implements View {

	protected $rules = array();
	protected static $expressions;

	static function registerExpression(Expression $expression) {
		self::$expressions[] = $expression;
	}

	function __construct(array $rules = null) {
		if ($rules) {
			foreach($rules as $rule=>$class) {
				$this->add($rule, $class);
			}
		}
	}

	function add($rule, $class) {
		$this->rules[$rule] = $class;
		return $this;
	}

	function render(Dispatch $dispatch) {
		if ($view = $this->resolve($dispatch)) {
			$dispatch->events->trigger("view-is-known", $view);
			$view->render($dispatch);
		}
		return $this;
	}

	function resolve(Dispatch $dispatch) {
		$patterns = array();
		foreach($this->rules as $rule=>$class) {
			foreach(self::$expressions as $expression) {
				if ($pattern = $expression->getPattern($rule, $class)) {
					$patterns[] = $pattern;
					break;
				}
			}
		}

		$matcher = new Matcher($patterns);
		$extractedData = array();
		if ($view = $matcher->match($dispatch->getComparableState(), $extractedData)) {
			if ($view instanceof View) {
				return $view;
			} elseif (is_string($view)) {
				return new $view;
			} elseif (is_callable($view)) {
				return $view();
			}
		}
	}
}
