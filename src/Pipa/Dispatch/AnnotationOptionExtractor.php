<?php

namespace Pipa\Dispatch;
use Pipa\Annotation\Reader;
use Pipa\Dispatch\Annotation\Option;
use Pipa\Registry\Registry;
use ReflectionFunction;
use ReflectionFunctionAbstract;

class AnnotationOptionExtractor implements OptionExtractor {
	
	protected static $namespaces = array();
	
	static function registerNamespace($namespace) {
		self::$namespaces[] = $namespace;
	}
		
	function getOptions(ReflectionFunctionAbstract $action) {
		$annotations = array();
		if (!($action instanceof ReflectionFunction) || $action->isClosure()) {
			foreach(self::$namespaces as $ns) {
				$reader = new Reader($ns);
				if ($action instanceof ReflectionFunction) {
					$annotations = array_merge($annotations, $reader->getFunctionAnnotations($action->name));
				} else {
					$annotations = array_merge(
						$annotations,
						$reader->getClassAnnotations($action->class),
						$reader->getMethodAnnotations($action->class, $action->name)
					);
				}
			}
		}
		
		$options = array();
		foreach($annotations as $annotation) {
			if ($annotation instanceof Option) {
				$options[$annotation->name] = $annotation->value;
			}
		}
		return $options;
	}
}
