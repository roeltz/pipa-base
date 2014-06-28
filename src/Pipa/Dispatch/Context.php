<?php

namespace Pipa\Dispatch;
use Pipa\Event\EventSource;

abstract class Context {
	
	protected static $hookpaths = array();
	
	public $events;
	
	static function registerHookpath($path) {
		self::$hookpaths[] = $path;
	}

	static function hook($_) {
		foreach(func_get_args() as $hook) {
			foreach(self::$hookpaths as $path) {
				if (file_exists($file = "$path/$hook.php")) {
					require_once $file;
					break;
				}
			}
		}
	}
	
	static function get() {
		return new static();
	}
	
	function __construct() {
		$this->events = new EventSource($this);
	}
}
