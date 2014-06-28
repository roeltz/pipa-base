<?php

namespace Pipa\Cache;

interface Cache {
	function get($key);
	function has($key);
	function set($key, $value);
	function remove($key);
	function destroy();
}
