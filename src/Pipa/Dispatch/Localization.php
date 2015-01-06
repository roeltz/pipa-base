<?php

namespace Pipa\Dispatch;
use Pipa\Locale\Locale;
use Pipa\Locale\Resource;

class Localization {
		
	private $extractors = array();
	
	static function attach(Dispatch $dispatch) {
		return new self($dispatch);
	}

	function __construct(Dispatch $dispatch) {
		$dispatch->events->listen("pre-routing", array($dispatch->localization = $this, "run"));
	}
	
	function accept($_) {
		Locale::accepted(func_get_args());
		return $this;
	}
	
	function extractor(LocaleExtractor $extractor) {
		$this->extractors[] = $extractor;
		return $this;
	}
	
	function resource($filename, $domain = "default") {
		Locale::registerResourceFilename($filename, $domain);
		return $this;
	}
	
	function run(Dispatch $dispatch) {
		foreach($this->extractors as $extractor) {
			if ($locale = $extractor->getLocale($dispatch)) {
				$locale->setEnvironment();
				$dispatch->locale = $locale;
				return;
			}
		}
	}
}
