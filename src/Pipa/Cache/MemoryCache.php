<?php

namespace Pipa\Cache;

class MemoryCache implements Cache {
	
	protected $cache = array();
	
	function get($key) {
		return @$this->cache[$key];
	}

	function has($key) {
		return isset($this->cache[$key]);
	}
	
	function set($key, $value) {
		$this->cache[$key] = $value;
		return $this;
	}
	
	function remove($key) {
		unset($this->cache[$key]);
		return $this;
	}
	
	function destroy() {
		unset($this->cache);
		$this->cache = array();
		return $this;
	}
}
