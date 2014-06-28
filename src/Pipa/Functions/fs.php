<?php

namespace Pipa;

// Taken from http://www.php.net/manual/en/function.glob.php#106595
function rglob($pattern, $flags = 0) {
	$files = glob($pattern, $flags);
	foreach (glob(dirname($pattern).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
		$files = array_merge($files, rglob($dir.DIRECTORY_SEPARATOR.basename($pattern), $flags));
	return $files;
}

function normalize_path($path) {
	return str_replace('/', DIRECTORY_SEPARATOR, preg_replace('#(\\\\|/)$#', '', $path));
}

// Taken from http://stackoverflow.com/a/2638272/1175382
function relative_path($from, $to) {
    $from     = explode(DIRECTORY_SEPARATOR, $from);
    $to       = explode(DIRECTORY_SEPARATOR, $to);
    $relPath  = $to;

    foreach($from as $depth => $dir) {
        // find first non-matching dir
        if($dir === $to[$depth]) {
            // ignore this directory
            array_shift($relPath);
        } else {
            // get number of remaining dirs to $from
            $remaining = count($from) - $depth;
            if($remaining > 1) {
                // add traversals up to first matching dir
                $padLength = (count($relPath) + $remaining - 1) * -1;
                $relPath = array_pad($relPath, $padLength, '..');
                break;
            } else {
                $relPath[0] = '.'.DIRECTORY_SEPARATOR . $relPath[0];
            }
        }
    }
    return implode(DIRECTORY_SEPARATOR, $relPath);
}