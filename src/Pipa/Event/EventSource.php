<?php

namespace Pipa\Event;

class EventSource {
	
	static protected $global;
	static protected $expected = array();
	
	protected $context;
	protected $events = array();
	protected $oneTimeEvents = array();

	static function expect($class, $event, $callback) {
		self::$expected[$class][$event][] = $callback;
	}
	
	static function getGlobal() {
		if (!self::$global)
			self::$global = new static(new stdClass);
		return self::$global;
	}
	
	function __construct($context) {
		$this->context = $context;
		foreach(self::$expected as $class=>$events) {
			if (is_a($context, $class)) {
				foreach($events as $event=>$callbacks) {
					foreach($callbacks as $callback) {
						$this->listen($event, $callback);
					}
				}
			}
		}
	}
	
	function listen($event, $callback) {
		$this->events[$event][] = $callback;
		return $this;
	}
	
	function trigger($event, $data = null, $once = false) {
		if ($once) {
			if (in_array($event, $this->oneTimeEvents)) return false;
			else $this->oneTimeEvents[] = $event;
		}

		if (isset($this->events[$event])) {
			foreach($this->events[$event] as $callback) {
				if (call_user_func($callback, $this->context, $data) === false) {
					return false;
				}
			}
		}
		return true;
	}
	
	function remove($event, $callback = null) {
		if (isset($this->events[$event])) {
			if ($callback) {
				foreach($this->events[$event] as $i=>$c) {
					if ($c == $callback) {
						unset($this->events[$event][$i]);
					}
				}
			} else {
				unset($this->events[$event]);
			}
		}
		return $this;
	}
}
