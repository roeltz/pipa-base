<?php

use Pipa\Config\Config;
use Pipa\Error\ErrorHandler;
use Pipa\Registry\Registry;
use Pipa\Dispatch\Action;
use Pipa\Dispatch\AnnotationOptionExtractor;

ErrorHandler::register();

$memoryCache = new Pipa\Cache\MemoryCache();
Registry::setLocked("cache", $memoryCache);

$fileCache = new Pipa\Cache\FileCache(Config::getFuture("cache.file.dir"));
Registry::setLocked("pcache", $fileCache);

AnnotationOptionExtractor::registerNamespace('Pipa\Dispatch\Annotation');
Action::registerOptionExtractor(new AnnotationOptionExtractor());
