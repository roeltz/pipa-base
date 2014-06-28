<?php

namespace Pipa\Registry;

require_once __DIR__ . '/Entry.php';

abstract class Registry {

	protected static $data;
	
	static function add($key, $value) {
		self::addByClass(__CLASS__, "default", $key, $value);
	}

	static function addByClass($class, $category, $key, $value) {
		self::setByClass($class, $category, $key, $value, Entry::FLAG_COLLECTION);
	}
	
	static function get($key) {
		return self::getByClass(__CLASS__, "default", $key, array_slice(func_get_args(), 1));
	}
	
	static function getAll() {
		return self::getAllByClass(__CLASS__, "default", false);
	}

	static function getAllByClass($class, $category, $keys = true, array $args = array()) {
		$compilation = array();
		foreach(self::$data as $registryClass=>$registryCategories) {
			if ($class == $registryClass || is_subclass_of($class, $registryClass)) {
				if (isset($registryCategories[$category])) {
					foreach($registryCategories[$category] as $registryKey=>$entry) {
						$compilation[$registryKey] = $entry->getValue($args);
					} 
				}
			}
		}
		
		if (!$keys)
			$compilation = array_values($compilation);
		
		return $compilation;
	}
	
	static function getByClass($class, $category, $key) {
		foreach(self::$data as $registryClass=>$registryCategories) {
			if ($class == $registryClass || is_subclass_of($class, $registryClass)) {
				if (isset($registryCategories[$category][$key])) {
					return self::getEntry($registryClass, $category, $key)->getValue();
				}
			}
		}
	}
	
	static function lock($key) {
		self::lockByClass(__CLASS__, "default", $key);
	}
	
	static function lockByClass($class, $category, $key) {
		self::getEntry($class, $category, $key)->lock();
	}

	static function set($key, $value, $locked = false) {
		self::setByClass(__CLASS__, "default", $key, $value, $locked ? Entry::FLAG_LOCKED : 0);
	}
	
	static function setByClass($class, $category, $key, $value, $flags = 0) {
		self::getEntry($class, $category, $key, $flags)->setValue($value);
	}

	static function setConstructor($key, $callback) {
		self::setSingletonByClass(__CLASS__, "default", $key, $callback);
	}
	
	static function setConstructorByClass($class, $category, $key, $callback) {
		self::setByClass($class, $category, $key, $callback, Entry::FLAG_CONSTRUCTOR);
	}
	
	static function setLocked($key, $value) {
		self::setByClass(__CLASS__, "default", $key, $value, Entry::FLAG_LOCKED);
	}
	
	static function setSingleton($key, $callback) {
		self::setSingletonByClass(__CLASS__, "default", $key, $callback);
	}

	static function setSingletonByClass($class, $category, $key, $callback) {
		self::setByClass($class, $category, $key, $callback, Entry::FLAG_SINGLETON);
	}
	
	static function __callStatic($key, array $args) {
		return self::getByClass(__CLASS__, "default", $key, $args);
	}
	
	protected static function getEntry($class, $category, $key, $flags = 0) {
		if (!isset(self::$data[$class][$category][$key]))
			self::$data[$class][$category][$key] = new Entry(null, $flags);
		
		return self::$data[$class][$category][$key]; 
	}
}
