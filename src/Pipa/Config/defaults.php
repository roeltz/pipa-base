<?php

namespace Pipa\Config;

function dateFormatFill($string) {
	return \Pipa\fill($string, array(
		'year'=>date('Y'),
		'month'=>date('m'),
		'week'=>date('W'),
		'day'=>date('d'),
		'hour'=>date('H'),
		'minutes'=>date('i'),
		'date'=>date('Ymd')
	));
}

Config::registerFilter("include", function(Config $config, $value, $key, $path){
	return $config->loadConfig(realpath(\Pipa\normalize_path(dirname($path)."/$value")));
});

Config::registerFilter("path", function(Config $config, $value, $key, $path){
	$value = dateFormatFill($value);
	return realpath(\Pipa\normalize_path(dirname($path)."/$value"));
});

Config::registerFilter("file", function(Config $config, $value, $key, $path){
	$path = dateFormatFill(\Pipa\normalize_path(dirname($path)."/$value"));
	if (!file_exists($path))
		touch($path);
	return realpath($path);
});

Config::registerFilter("const", function(Config $config, $value, $key, $path){
	return constant($value);
});
