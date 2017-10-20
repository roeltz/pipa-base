<?php

namespace Pipa\Locale {

	function translate($message, $parameters = array(), $domain = "default") {
		if ($locale = Locale::get()) {
			return \Pipa\fill($locale->translate($message, $domain), $parameters);
		} else {
			return \Pipa\fill($message, $parameters);
		}
	}
}

namespace {

	if (!function_exists("__")) {
		function __() {
			call_user_func_array("Pipa\Locale\translate", func_get_args());
		}
	}
}
