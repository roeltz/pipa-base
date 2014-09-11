<?php

namespace Pipa\Dispatch;
use Pipa\Dispatch\Exception\SecurityException;

class Security {

	private $roles = array();
	private $reverse = array();

	static function attach(Dispatch $dispatch) {
		$instance = new self();
		$dispatch->security = $instance;
		$dispatch->events->listen("post-routing", array($instance, "checkDispatch"));
		return $instance;
	}
	
	function checkDispatch(Dispatch $dispatch) {
		if (!$dispatch->session->getPrincipal()) {
			$dispatch->events->trigger("principal-needed");
		}
		
		$principal = $dispatch->session->getPrincipal();
		$constraints = $dispatch->action->getOptions("secured");

		if (!$this->isAllowed($principal, $constraints)) {
			throw new SecurityException("User not allowed");
		}
	}

	function role($role, $_ = null) {
		$this->roles[$role] = array_slice(func_get_args(), 1);
		$this->reverse[$role] = array();
		$this->computeReverseRoles();
		return $this;
	}

	function isAllowed($principal, $constraints = null) {

		if ($constraints === false)
			return true;

		if ($principal instanceof Principal) {
			$roles = (array) $principal->getPrincipalRoles();
		} else {
			$roles = array();
		}

		if ($constraints) {
			if ($constraints == "*" && $principal) {
				return true;
			} else {
				$constraints = (array) $constraints;
				$configAllowed = array();
				$configDenied = array();
				foreach($constraints as $role) {
					if (strpos($role, "!") !== false) {
						$configDenied[] = $role;
					} else {
						$configAllowed[] = $role;
					}
				}

				$allow = (in_array("*", $configAllowed) && $principal) || $this->isAnyRoleIncluded($roles, $configAllowed);
				$deny = in_array("*", $configDenied) || $this->isAnyRoleIncluded($roles, $configDenied);
				return in_array("#denyfirst", $constraints) ? (!$deny || $allow) : ($allow && !$deny);
			}
		}
		
		return true;
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
