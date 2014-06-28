<?php

namespace Pipa\Dispatch;
use Pipa\Config\Config;

abstract class Session {
	
	const PRINCIPAL_KEY_CONFIG_PATH = "session.principal-key";
		
	private $principal;
	
	abstract function get($key);
	abstract function set($key, $value);
	abstract function has($key);
	abstract function remove($key);
	abstract function destroy();
	
	function setLifetime($seconds) {}
	
	function getPrincipal() {
		if (!$this->principal)
			$this->principal = $this->get(Config::get(self::PRINCIPAL_KEY_CONFIG_PATH));
		return $this->principal;
	}
	
	function setPrincipal($principal) {
		$this->principal = $principal;
		$this->set(Config::get(self::PRINCIPAL_KEY_CONFIG_PATH), $principal);
	}
	
	function unsetPrincipal() {
		$this->principal = null;
		$this->remove(Config::get(self::PRINCIPAL_KEY_CONFIG_PATH));
	}
}
