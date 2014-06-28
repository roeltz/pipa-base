<?php

namespace Pipa\Dispatch;

class Response {
	
	function render(Dispatch $dispatch) {
		if ($dispatch->view)
			$dispatch->view->render($dispatch);
		return $this;
	}
}
