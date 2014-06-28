<?php

namespace Pipa;

function object_walk_recursive(&$object, $callback, $k = null, &$source = null, array &$visited = array()) {
	if (is_object($object) && in_array($object, $visited, true)) return;
	elseif (is_object($object)) $visited[] = $object;

	$result = $callback($object, $k);

	if ($result !== false && (is_object($object) || is_array($object)))
		foreach($object as $k=>&$v)
			object_walk_recursive($v, $callback, $k, $object, $visited);
}

function object_remove_recursion(&$object, &$stack = array()) {
	if ((is_object($object) || is_array($object)) && $object) {
        if (!in_array($object, $stack, true)) {
            $stack[] = $object;
            foreach ($object as &$subobject) {
                object_remove_recursion($subobject, $stack);
            }
			array_pop($stack);
        } else {
            $object = null;
        }
    }
    return $object;
}

function traverse(&$object, $propertyList, $newValue = null) {
	$propertyList = is_array($propertyList) ? $propertyList : explode(".", $propertyList);
	$write = count(func_get_args()) == 3;
	
	if ($propertyList) {
		$value = null;
		
		if (is_object($object)) {
			$value = &$object->{$propertyList[0]};
		} elseif (is_array($object)) {
			$value = &$object[$propertyList[0]];
		}
		
		if (count($propertyList) == 1) {
			if ($write) {
				$value = $newValue;
			} else {
				return $value;
			}
		} else if ($value !== null)
			return traverse($value, array_slice($propertyList, 1), $newValue);
		else
			return null;
	}
}
