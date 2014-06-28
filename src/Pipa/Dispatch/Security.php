<?php

namespace Pipa\Dispatch;
use Pipa\Dispatch\Exception\SecurityException;

class Security {

	private $roles = array();
	private $reverse = array();
	static private $instance;

	static function attach(Dispatch $dispatch) {
		return self::$instance = new self($dispatch);
	}
	
	static function getInstance() {
		return self::$instance;
	}

	private function __construct(Dispatch $dispatch) {
		$dispatch->events->listen("post-routing", array($dispatch->security = $this, "check"));
	}

	function check(Dispatch $dispatch) {
		if (!$dispatch->session->getPrincipal()) {
			$dispatch->events->trigger("principal-needed");
		}

		if (!$this->isAllowed($dispatch)) {
			throw new SecurityException("User not allowed");
		}
	}

	function role($role, $_ = null) {
		$this->roles[$role] = array_slice(func_get_args(), 1);
		$this->reverse[$role] = array();
		$this->computeReverseRoles();
		return $this;
	}

	function isAllowed(Dispatch $dispatch, $allowedRoles = null) {
		$allowed = true;
		$configured = $dispatch->action->getOptions('secured');
		$principal = $dispatch->session->getPrincipal();
		$roles = array();

		if ($configured === false)
			return true;

		if ($principal instanceof Principal)
			$roles = (array) $principal->getPrincipalRoles();

		if ($allowedRoles) {
			$allowed = $this->isAnyRoleIncluded($roles, (array) $allowedRoles);
		} elseif ($configured) {
			if ($configured == "*" && $principal) {
				$allowed = true;
			} else {
				$configured = (array) $configured;
				$configAllowed = array();
				$configDenied = array();
				foreach($configured as $role) {
					if (strpos($role, "!") !== false) {
						$configDenied[] = $role;
					} else {
						$configAllowed[] = $role;
					}
				}

				$allow = (in_array("*", $configAllowed) && $principal) || $this->isAnyRoleIncluded($roles, $configAllowed);
				$deny = in_array("*", $configDenied) || $this->isAnyRoleIncluded($roles, $configDenied);
				$allowed = in_array("#denyfirst", $configured) ? (!$deny || $allow) : ($allow && !$deny);
			}
		}
		return $allowed;
	}

	function getGreaterRoles($role) {
		$greater = $this->reverse[$role];
		foreach($this->reverse[$role] as $r)
			$greater = array_unique(array_merge($greater, $this->getGreaterRoles($r)));
		return $greater;
	}
	
	function getLesserRoles($role) {
		$lesser = $this->roles[$role];
		foreach($this->roles[$role] as $r)
			$lesser = array_unique(array_merge($lesser, $this->getLesserRoles($r)));
		return $lesser;		
	}

	private function isAnyRoleIncluded(array $concrete, array $annotated) {
		foreach($concrete as $c) {
			$pattern = "/^$c\$/";
			foreach($annotated as $a)
				if (preg_match($pattern, $a))
					return true;
		}
		foreach($annotated as $a) {
			foreach((array) @$this->reverse[$a] as $r) {
				$pattern = "/^$r\$/";
				foreach($concrete as $c) {
					if (preg_match($pattern, $c))
						return true;
				}
			}
		}
	}

	private function computeReverseRoles() {

		foreach(array_keys($this->roles) as $role) {
			$stack = array($role);
			while($stack) {
				$item = array_shift($stack);
				foreach($this->roles as $r=>$rs) {
					if (in_array($item, $rs)) {
						if (!in_array($r, $this->reverse[$role])) {
							$this->reverse[$role][] = $r;
							$stack[] = $r;
						}
					}
				}
			}
		}

		foreach($this->reverse as $r=>$rs)
			foreach($rs as $rr)
				$this->reverse[$r] = array_unique(array_merge($this->reverse[$r], $this->reverse[$rr]));
	}
}
