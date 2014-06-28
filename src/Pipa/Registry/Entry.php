<?php

namespace Pipa\Registry;
use LogicException;

class Entry {
	
	const FLAG_CONSTRUCTOR = 1;
	const FLAG_SINGLETON = 2;
	const FLAG_COLLECTION = 4;
	const FLAG_LOCKED = 8;
	
	protected $flags;
	protected $initialized = false;
	protected $singleton;
	protected $value;
	
	function __construct($value = null, $flags = 0) {
		if (($flags & self::FLAG_COLLECTION) && !is_array($value) && !is_null($value))
			$value = array($value);

		$this->value = $value;
		$this->flags = $flags;
	}
	
	function getFlags() {
		return $this->flags;
	}
	
	function getValue(array $args = array()) {
		if ($this->flags & self::FLAG_CONSTRUCTOR) {
			return call_user_func_array($this->value, $args);
		} elseif ($this->flags & self::FLAG_SINGLETON) {
			if (!$this->singleton)
				$this->singleton = call_user_func_array($this->value, $args);
			return $this->singleton; 
		} else {
			return $this->value;
		}
	}
	
	function lock() {
		$this->flags |= self::FLAG_LOCKED;
		return $this;
	}

	function setFlags($flags) {
		if (!($this->flags & self::FLAG_LOCKED)) {
			$this->flags = $flags;
			return $this;
		} else {
			throw new LogicException("Registry entry is locked");
		}
	}
	
	function setValue($value) {
		if (!($this->flags & self::FLAG_LOCKED) || !$this->initialized) {
			if ($this->flags & self::FLAG_COLLECTION) {
				$this->value[] = $value;
			} else {
				$this->value = $value;
			}
			$this->initialized = true;
			return $this;
		} else {
			throw new LogicException("Registry entry is locked");
		}
	}
}
