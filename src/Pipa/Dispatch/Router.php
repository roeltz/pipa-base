<?php

namespace Pipa\Dispatch;

interface Router {
	function resolve(Dispatch $dispatch);
}
