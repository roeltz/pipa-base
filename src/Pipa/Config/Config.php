<?php

namespace Pipa\Config;

class Config {
	
	const PATTERN_FILTER_SUFFIX = '/#(\w+)$/';
	
	protected static $global;
	protected static $filters = array();
	
	protected $config = array();
	
	static function getGlobal() {
		if (!self::$global)
			self::$global = new self;
		return self::$global;
	}
	
	static function get($key, $default = null) {
		return self::getGlobal()->getValue($key, $default);
	}
	
	static function getFuture($key) {
		return self::getGlobal()->getFutureValue($key);
	}
	
	static function load() {
		foreach(func_get_args() as $path) {
			if (file_exists($path)) {
				return self::getGlobal()->loadFile($path);
			}
		}
	}
	
	static function set($key, $value) {
		return self::getGlobal()->setValue($key, $value);
	}
	
	static function registerFilter($suffix, $filter) {
		self::$filters[$suffix] = $filter;
	}
	
	function getValue($key, $default = null) {
		$value = $this->lookup($key);
		return $value ? $value : $default;
	}
	
	function getFutureValue($key) {
		return new FutureValue($this, $key);
	}
		
	function loadFile($path) {
		$this->config = array_merge_recursive($this->config, $this->loadConfig($path));
		return $this;
	}
	
	function loadConfig($path) {
		$config = json_decode(file_get_contents($path), true);
		$this->filter($config, $path);
		return $config;
	}
	
	function setValue($key, $value) {
		$key = &$this->lookup($key);
		$key = $value;
		return $this;
	}
	
	protected function filter(array &$config, $path) {
		$self = $this;
		$class = get_called_class();
		$filters = self::$filters;
		\Pipa\object_walk_recursive($config, function(&$v, $k) use($self, $class, $path, $filters){
			if (is_array($v)) {
				foreach($v as $k=>$x) {
					if (preg_match(Config::PATTERN_FILTER_SUFFIX, $k, $m)) {
						$kk = preg_replace(Config::PATTERN_FILTER_SUFFIX, '', $k);
						if ($filter = @$filters[$m[1]])
							$x = $filter($self, $x, $kk, $path);
						$v[$kk] = $x;
						unset($v[$k]);
					}
				}
			}
		});
	}

	protected function &lookup($key) {
		$current = &$this->config;
		$components = explode(".", $key);
		while($components) {
			$level = array_shift($components);
			if (!isset($current[$level])) {
				$current[$level] = array();
			} 
			$current = &$current[$level];
			if (!is_array($current)) break;
		}
		return $current;
	}
}

require_once __DIR__."/defaults.php";
