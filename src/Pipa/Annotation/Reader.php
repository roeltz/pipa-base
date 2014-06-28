<?php

namespace Pipa\Annotation;
use Pipa\Parser\Parser;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * Class for reading annotations embedded in doc blocks
 */
class Reader {
	
	static protected $cache = array();
	static protected $nodeCache = array();
	protected $namespace;
	
	/**
	 * @param string $ns Base namespace for all annotations to be recognized with this instance
	 */
	function __construct($ns = "") {
		$this->namespace = $ns;
	}
	
	/**
	 * @internal
	 */
	function cast(AnnotationNode $node, $target) {
		if (class_exists($class = "{$this->namespace}\\{$node->class}", true)) {
			$self = $this;
			if ($values = $node->values) {
				\Pipa\object_walk_recursive($values, function(&$v) use($self, $class){
					if ($v instanceof AnnotationNode) {
						$v = $self->cast($v);
						return false;
					} elseif ($v instanceof AnnotationConstNode) {
						switch($v->type) {
							case AnnotationConstNode::TYPE_GLOBAL:
								if (defined($v->name)) {
									$v = constant($v->name);
								} elseif (defined($name = "{$class}::{$v->name}")) {
									$v = constant($name);
								}
								break;
							case AnnotationConstNode::TYPE_LOCAL:
								$v = constant("{$class}::{$v->name}");
								break;
							case AnnotationConstNode::TYPE_SCOPED:
								$v = constant($v->name);
								break;
						}
						return false;
					}
				});
			} else {
				$defaultValues = get_class_vars($class);
				$values = array();
			}
			$annotation = new $class($values);
			$annotation->check($target);
			return $annotation;
		}
	}
	
	function getAnnotatonFromString($string, $annotation) {
		return current($this->getAnnotationsFromString($string, $annotation));
	}
	
	function getAnnotationsFromString($string, $annotation = null) {
		return $this->filter($annotation, $this->process($string));
	}

	/**
	 * Returns a single annotation for a class
	 * 
	 * @param string $class The fully qualified name of the class containing the annotation
	 * @param string $annotation The name of the requested annotation without the namespace portion given to the Reader constructor
	 */
	function getClassAnnotation($class, $annotation) {
		return current($this->getClassAnnotations($class, $annotation));
	}

	/**
	 * Returns all annotations for a class, optionally filtered by annotation type
	 * 
	 * @param string $class The fully qualified name of the class containing the annotation
	 * @param string $annotation The name of the annotation without the namespace portion given to the Reader constructor
	 */	
	function getClassAnnotations($class, $annotation = null) {
		if (!isset(self::$cache['class'][$this->namespace][$class])) {
			$rclass = new ReflectionClass($class);
			self::$cache['class'][$this->namespace][$class] = $this->process($rclass->getDocComment(), $rclass);
		}
		return $this->filter($annotation, self::$cache['class'][$this->namespace][$class]);
	}
	
	/**
	 * Returns a single annotation for a method
	 * 
	 * @param string $class The fully qualified name of the method's class
	 * @param string $method The name of the method containing the annotation
	 * @param string $annotation The name of the requested annotation without the namespace portion given to the Reader constructor
	 */	
	function getMethodAnnotation($class, $method, $annotation) {
		return current($this->getMethodAnnotations($class, $method, $annotation));
	}
	
	/**
	 * Returns all annotations for a method, optionally filtered by annotation type
	 * 
	 * @param string $class The fully qualified name of the method's class
	 * @param string $method The name of the method containing the annotation
	 * @param string $annotation The name of the annotation without the namespace portion given to the Reader constructor
	 */	
	function getMethodAnnotations($class, $method, $annotation = null) {
		if (!isset(self::$cache['method'][$this->namespace][$class][$method])) {
			$rmethod = new ReflectionMethod($class, $method);
			self::$cache['method'][$this->namespace][$class][$method] = $this->process($rmethod->getDocComment(), $rmethod);
		}
		return $this->filter($annotation, self::$cache['method'][$this->namespace][$class][$method]);
	}

	/**
	 * Returns a single annotation for a property
	 * 
	 * @param string $class The fully qualified name of the property's class
	 * @param string $property The name of the method containing the annotation
	 * @param string $annotation The name of the requested annotation without the namespace portion given to the Reader constructor
	 */		
	function getPropertyAnnotation($class, $property, $annotation) {
		return current($this->getPropertyAnnotations($class, $property, $annotation));
	}

	/**
	 * Returns all annotations for a property, optionally filtered by annotation type
	 * 
	 * @param string $class The fully qualified name of the property's class
	 * @param string $property The name of the method containing the annotation
	 * @param string $annotation The name of the annotation without the namespace portion given to the Reader constructor
	 */	
	function getPropertyAnnotations($class, $property, $annotation = null) {
		if (!isset(self::$cache['property'][$this->namespace][$class][$property])) {
			$rproperty = new ReflectionProperty($class, $property);
			self::$cache['property'][$this->namespace][$class][$property] = $this->process($rproperty->getDocComment(), $rproperty);
		}
		return $this->filter($annotation, self::$cache['property'][$this->namespace][$class][$property]);
	}

	function getFunctionAnnotation($class, $annotation) {
		return current($this->getFunctionAnnotations($class, $annotation));
	}

	function getFunctionAnnotations($function, $annotation = null) {
		try {
			if (!isset(self::$cache['function'][$this->namespace][$function])) {
				$rfunction = new ReflectionFunction($function);
				self::$cache['function'][$this->namespace][$function] = $this->process($rfunction->getDocComment(), $rfunction);
			}
			return $this->filter($annotation, self::$cache['function'][$this->namespace][$function]);
		} catch(ReflectionException $e) {
			return array();
		}
	}
	
	protected function filter($annotation, array $annotations) {
		if (!$annotation) return $annotations;
		return array_filter($annotations, function($a) use($annotation){
			return preg_match('/(^|\\\\)'.preg_quote($annotation).'$/', get_class($a));
		});
	}
	
	protected function parse($string) {
		$parser = new Parser(new AnnotationGrammar());
		$nodes = $parser->parse($string);
		return $nodes;
	}

	protected function process($string, $target = null) {
		$annotations = array();
		if ($string) {
			foreach($this->parse($string) as $node) {
				$annotations[] = $this->cast($node, $target);
			}
		}
		return $annotations;
	}
}
