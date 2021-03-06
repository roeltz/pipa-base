<?php

namespace Pipa\Dispatch;
use Pipa\Dispatch\Exception\RoutingException;
use Pipa\Match\HasComparableState;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;

class Action implements HasComparableState {

	const METHOD_PATTERN = '/^((?:\w+(?:\\\\\w+)*)?\w+)::(\w+)$/';

	protected $callable;
	protected static $extractors = array();

	static function registerOptionExtractor(OptionExtractor $extractor) {
		self::$extractors[] = $extractor;
	}

	function __construct($callable) {
		$this->callable = $callable;
	}

	function castParameter(ReflectionParameter $parameter, $value) {
		if ($class = $parameter->getClass()) {
			if ($class->getName() == "DateTime") {
				if (is_numeric($value)) {
					$date = new \DateTime();
					$date->setTimestamp($value);
					return $date;
				} elseif (strlen($value)) {
					return new \DateTime($value);
				} else {
					return null;
				}
			} elseif ($class->implementsInterface(__NAMESPACE__.'\Parameter')) {
				$instance = $class->newInstance();
				$instance->useParameterValue($value);
				return $instance;
			}
		}
		return $value;
	}

	function getComparableState() {
		$state = array();
		$options = $this->getOptions();
		foreach($options as $name=>$value) {
			$state["option:$name"] = $value;
		}
		return $state;
	}

	function getOptions($name = null) {
		$options = array();
		$reflector = $this->getReflector();
		foreach(self::$extractors as $extractor) {
			$options = array_merge($options, $extractor->getOptions($reflector));
		}
		if ($name) {
			return @$options[$name];
		} else {
			return $options;
		}
	}

	function getInstance(ReflectionMethod $method) {
		if ($method->isStatic()) {
			return null;
		} elseif (is_array($this->callable) && is_object($this->callable[0])) {
			return $this->callable[0];
		} else {
			return $method->getDeclaringClass()->newInstance();
		}
	}

	function getParameterList(ReflectionFunctionAbstract $function, Dispatch $dispatch) {
		$parameters = array();
		foreach($function->getParameters() as $parameter) {
			$name = $parameter->getName();
			$class = $parameter->getClass();

			if (!is_null($value = @$dispatch->request->data[$name])) {
				$parameters[] = $this->castParameter($parameter, $value);
			} elseif ($parameter->isOptional()) {
				$parameters[] = $parameter->getDefaultValue();
			} elseif ($class) {
				if ($class->isInstance($dispatch)) {
					$parameters[] = $dispatch;
				} else {
					foreach($dispatch as $property=>$value) {
						if ($value && $class->isInstance($value)) {
							$parameters[] = $value;
							break;
						}
					}
				}
			} else {
				$parameters[] = null;
			}
		}
		return $parameters;
	}

	function getReflector() {
		if (is_callable($this->callable, false, $name)) {
			if ($name == 'Closure::__invoke') {
				return new ReflectionFunction($this->callable);
			} elseif (preg_match(self::METHOD_PATTERN, $name, $m)) {
				return new ReflectionMethod($m[1], $m[2]);
			} else {
				return new ReflectionFunction($name);
			}
		} else {
			throw new RoutingException("Invalid action");
		}
	}

	function invoke(ReflectionFunctionAbstract $function, array $parameters) {
		if ($function instanceof ReflectionMethod) {
			return $function->invokeArgs($this->getInstance($function), $parameters);
		} else {
			return $function->invokeArgs($parameters);
		}
	}

	function run(Dispatch $dispatch) {
		$function = $this->getReflector();
		$parameters = $this->getParameterList($function, $dispatch);
		return $this->invoke($function, $parameters);
	}
}
