<?php

namespace Pipa\Config;

class FutureValue {
	
	private $config;
	private $key;
	
	function __construct(Config $config, $key) {
		$this->config = $config;
		$this->key = $key;
	}
	
	function __toString() {
		return (string) $this->config->get($this->key);
	}
}
