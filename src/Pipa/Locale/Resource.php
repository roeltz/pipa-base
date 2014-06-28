<?php

namespace Pipa\Locale;

abstract class Resource {
	protected $path;
	
	final function __construct($path) {
		$this->path = $path;
	}
	
	protected function getLocalizedPath($localeCode) {
		return \Pipa\interpolate($this->path, function($k) use($localeCode) { return $k == "locale" ? $localeCode : $k; });
	}
	
	abstract function getMessage($message, $localeCode);
}
