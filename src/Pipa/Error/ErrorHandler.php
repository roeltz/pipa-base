<?php

namespace Pipa\Error;

abstract class ErrorHandler {

	protected static $context;
	protected static $displays;
	protected static $errorControlOperatorEnabled = false;

	static function addDisplay(ErrorDisplay $display, $context = 'all') {
		self::$displays[$context][] = $display;
	}

	static function display(ErrorInfo $info) {
		self::displayForContext('all', $info);
		self::displayForContext(self::$context, $info);
	}

	static function displayForContext($context, ErrorInfo $info) {
		if (isset(self::$displays[$context])) {
			foreach(self::$displays[$context] as $display) {
				$display->display($info);
			}
		}
	}

	static function enableErrorControlOperatorDisplay($enable = true) {
		self::$errorControlOperatorEnabled = $enable;
	}

	static function handleError($code, $message, $file, $line) {
		if (!(error_reporting() & $code) && !self::$errorControlOperatorEnabled) return false;
		self::display(new ErrorInfo($message, $code, $file, $line, array_slice(debug_backtrace(), 1)));
	}

	static function handleException($e) {
		self::display(new ExceptionInfo($e));
		return false;
	}

	static function register($errorTypes = null) {
		set_error_handler(__CLASS__.'::handleError', is_null($errorTypes) ? error_reporting() : $errorTypes);
		set_exception_handler(__CLASS__.'::handleException');
	}

	static function setContext($context) {
		self::$context = $context;
	}
}
