#!/usr/bin/php
<?php

	if ($argv[1] == 'create') {
		$phar = new Phar($argv[2], FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME);
		
		$iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($argv[3]));
		foreach($iter as $oFile) {
			false && $oFile = new SplFileInfo();
			
			$ok = true;
			foreach(explode('/',$oFile->getPathname()) as $p) {
				if ($p == '.' || $p == '..') continue;
				if (substr($p,0,1) == '.') $ok = false;
			}			
			if (!$ok) continue;
			if ($oFile->getFilename() == '.') continue;
			if ($oFile->getFilename() == '..') continue;
			
			
			$f = $oFile->getPathname();
			if (substr($f,0,strlen($argv[3])) == $argv[3]) {
				$f = substr($f,strlen($argv[3]));
			}
			
			printf("%s -> %s\n", $oFile->getPathname(), $f);
			$phar->addFile($oFile->getPathname(), $f);
		}
		
		exit(0);
	}
	if ($argv[1] == 'list') {
		$phar = new Phar($argv[2]);
		foreach($phar as $f) {
			var_dump($f);
		}
	}
	if ($argv[1] == 'extract') {
		$phar = new Phar($argv[2]);
		$phar->extractTo($argv[3]);
	}


?>
