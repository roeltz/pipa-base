<?php

namespace Pipa\Dispatch;
use ReflectionFunctionAbstract;

interface OptionExtractor {
	function getOptions(ReflectionFunctionAbstract $action);
}
