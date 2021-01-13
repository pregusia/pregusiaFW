<?php
/**
 *  This file is part of PREGUSIA-PHP-FRAMEWORK.
 *  PREGUSIA-PHP-FRAMEWORK is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation; either version 2.1 of the License, or
 *  (at your option) any later version.
 *  
 *  PREGUSIA-PHP-FRAMEWORK is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *  
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with PREGUSIA-PHP-FRAMEWORK; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *  
 *  @author pregusia
 *
 */


class UtilsIO {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @throws IOException
	 */
	public static function rmRecursive($path) {
		$path = rtrim($path,'/');
		$res = false;
		
	    if (is_dir($path) && !is_link($path)) {
	        if ($dh = opendir($path)) {
	            while (($sf = readdir($dh)) !== false) {
	                if ($sf == '.' || $sf == '..') {
	                    continue;
	                }
	                self::rmRecursive($path.'/'.$sf);
	            }
	            closedir($dh);
	        }
	        $res = @rmdir($path);
	    } else {
	    	$res = @unlink($path);
	    }
	    
	    if (!$res) {
	    	throw new IOException(sprintf('Path %s could not be deleted', $path));
	    }
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @param string $pattern
	 * @return string[]
	 */
	public static function findRecursive($path, $pattern) {
		$res = array();
		$path = rtrim($path,'/') . '/';
		
		if (is_dir($path)) {
			if ($dh = opendir($path)) {
	            while (($sf = readdir($dh)) !== false) {
	                if ($sf == '.' || $sf == '..') {
	                    continue;
	                }
	                
	                if (is_dir($path . $sf)) {
	                	$tmp = self::findRecursive($path . $sf, $pattern);
	                	foreach($tmp as $v) $res[] = $v;
	                } else {
						if (preg_match($pattern, $sf)) {
		                	$res[] = $path . $sf;
		                }
	                }
	            }
	            closedir($dh);
	        }
		}
		
		return $res;
	}
		
	//************************************************************************************
	/**
	 * Uruchamia wskazany proces przechwytujac z niego STDOUT
	 * @param string $cmdline Sciezka do pliku
	 * @param string $stdin dane do STDIN
	 * @return string dane z STDOUT
	 */
	public static function executeProcess($cmdline, $stdin='') {
		$cmdline = trim($cmdline);
		if (!$cmdline) throw new InvalidArgumentException('Empty cmdline');
		
		$file = UtilsArray::getFirst(explode(' ', $cmdline));
		if (!is_file($file)) throw new IOException(sprintf('File %s not found', $file));
		if (!is_executable($file)) throw new IOException(sprintf('File %s is not executable', $file));
		
		$descriptorsSpec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("file", "/dev/null", "a") // stderr is a file to write to
		);
		$pipes = array();
		$process = proc_open($cmdline, $descriptorsSpec, $pipes);
		
		if (is_resource($process)) {
			$pW = $pipes[0];
			$pR = $pipes[1];
			
			if ($stdin) {
				fwrite($pw, $stdin);
			}
			
			fclose($pW);
			
			$res = stream_get_contents($pR);
			fclose($pR);
			proc_close($process);			
			
			return $res;
		} else {
			throw new IOException(sprintf('Could not create process from %s', $cmdline));
		}
	}
}

?>