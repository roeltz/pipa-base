<?php

namespace Pipa\Locale;

Locale::registerResourceClass('Pipa\Locale\MoResource', function($path){
	return preg_match('/\.mo$/i', $path);
});
