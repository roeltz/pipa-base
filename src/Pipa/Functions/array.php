<?php

namespace Pipa;

function array_flatten(array $array, $filterNulls = true) {
	$flat = (object) array('a'=>array());
	array_walk_recursive($array, function(&$v, $k) use ($flat, $filterNulls) { if (!$filterNulls || !is_null($v)) $flat->a[] = $v; });
	return $flat->a;
}

function array_remove(array &$array, $value) {
	foreach($array as $i=>$v)
		if ($v === $value)
			unset($array[$i]);
}

function array_key_splice(array &$array, $key, array $values = array()){
	$keys = array_keys($array);
	$offset = array_search($key, $keys);
	$extracted = $array[$keys[$offset]];
	$array = array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset + 1, null, true);
	return $extracted;
}

function array_to_camel_case_keys(array $array) {
	$array2 = array();
	foreach($array as $k=>&$v) {
		$array2[to_camel_case(strtolower($k))] = $v;
	}
	return $array2;
}

function array_walk_recursive_path(array $array, $callback, $leavesOnly = false, array &$stack = null) {
	if (!$stack) $stack = array();
	foreach($array as $k=>&$v) {
		$stack[] = $k;
		if (!$leavesOnly || !is_array($v))
			$callback($v, $stack);
		if (is_array($v))
			array_walk_recursive_path($v, $callback, $leavesOnly, $stack);
		array_pop($stack);
	}
}

function is_assoc($array) {
	if (is_array($array))
		return (bool) count(array_filter(array_keys($array), 'is_string'));
	else
		return false;
}

function to_array($value) {
	if (is_array($value))
		return $value;
	elseif (is_null($value))
		return array();
	else
		return array($value);
}
