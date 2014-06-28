<?php

namespace Pipa\Error;

class HTMLErrorDisplay implements ErrorDisplay {
		
	function display(ErrorInfo $info) {
		echo "<div><strong>{$info->message}</strong><br>{$info->file}:{$info->line}</div>\n";
	}
}
