<?php

foreach(glob(__DIR__."/*.php") as $file) {
	if (basename($file) != "all.php") {
		require_once $file;
	}
}
