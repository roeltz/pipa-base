<?php

namespace Pipa\Filesystem;

class CascadingFilesystem {
	
	const ONLY_FILES = 1;
	const ONLY_DIRS = 2;
	const RECURSIVE = 4;
	
	protected $roots = array();
	protected $ordered = true;
	
	function addRoot($path, $priority = 10) {
		$this->roots[] = array('path'=>\Pipa\normalize_path($path), 'priority'=>$priority);
		$this->ordered = false;
	}
	
	function getFile($path) {
		if (!$this->ordered)
			$this->sortRoots();
		
		foreach($this->roots as $root) {
			if (file_exists($filename = "{$root['path']}/$path")) {
				return new File(realpath($filename));
			}
		}
	}
	
	function getListing($path = "", $flags = 0) {
		$listing = array();
		foreach($this->roots as $root) {
			$subpath = \Pipa\normalize_path("{$root['path']}/$path");
			foreach(glob($subpath.DIRECTORY_SEPARATOR."*") as $filename) {
				$virtualFilename = \Pipa\remove_from_beggining($root['path'].DIRECTORY_SEPARATOR, $filename);
				if ((!($flags & self::ONLY_DIRS & self::ONLY_FILES))
					|| (($flags & self::ONLY_DIRS) && is_dir($filename))
					|| (($flags & self::ONLY_FILES) && is_file($filename))) {
					$listing[$virtualFilename] = new File(realpath(\Pipa\normalize_path($filename)));
				}
				
				if (($flags & self::RECURSIVE) && is_dir($filename)) {
					$listing = array_merge($this->getListing($virtualFilename, $flags), $listing);
				}
			}
		}
		ksort($listing);
		return $listing;
	}
	
	private function sortRoots() {
		usort($this->roots, function($a, $b){
			return $b['priority'] - $a['priority'];
		});
		$this->ordered = true;
	}
}
