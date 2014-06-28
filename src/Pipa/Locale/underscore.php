<?php

use Pipa\Locale\Locale;

function __($message, $parameters = array(), $domain = "default") {
	if ($locale = Locale::get()) {
		return \Pipa\fill($locale->translate($message, $domain), $parameters);
	} else {
		return \Pipa\fill($message, $parameters);
	}
}
