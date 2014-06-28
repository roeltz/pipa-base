<?php

namespace Pipa\Dispatch;

class PHPSession extends Session {

	public $id;
	private $set = false;

	function __construct($id = null) {
		if ($id) {
			$this->id = $id;
			$this->set = true;
		} else {
			$this->id = session_id();
		}
	}

	function get($key) {
		$this->setSession();
		return @$_SESSION[$key];
	}
	
	function set($key, $value) {
		$this->setSession();
		return @$_SESSION[$key] = $value;
	}
	
	function has($key) {
		$this->setSession();
		return isset($_SESSION[$key]);
	}
	
	function remove($key) {
		$this->setSession();
		unset($_SESSION[$key]);
	}
	
	function destroy() {
		$this->setSession();
		session_destroy();
	}

	function setLifetime($seconds) {
		session_set_cookie_params($seconds);
		session_write_close();
		session_id($this->id);
		@session_start();
	}

	private function setSession() {
    	if ($this->set && session_id() != $this->id) {
    		session_write_close();
    		session_id($this->id);
		}		
   		@session_start();
	}
}
