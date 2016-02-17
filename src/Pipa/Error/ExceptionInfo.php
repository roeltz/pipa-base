<?php

namespace Pipa\Error;
use Throwable;

class ExceptionInfo extends ErrorInfo {
	
	function __construct(Throwable $e) {
		parent::__construct(
			get_class($e).': '.$e->getMessage(),
			$e->getCode(),
			$e->getFile(),
			$e->getLine(),
			$e->getTrace()
		);
	}
}
