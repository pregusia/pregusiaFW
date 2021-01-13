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


class Lock {
	
	private $file = null;
	private $path = '';
	
	//************************************************************************************
	private function __construct($f, $path) {
		$this->file = $f;
		$this->path = $path;
	}
	
	//************************************************************************************
	public function unlock() {
		if (is_resource($this->file)) {
            fclose($this->file);
            @unlink($this->path);
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return Lock
	 */
	public static function acquire($name,$block=false) {
		$path = '/tmp/php_lock_' . $name . '.lock';
		
		$f = fopen($path, 'w');
		if (!$f) return null;
		
		$flags = 0;
		$flags |= LOCK_EX;
		if (!$block) $flags |= LOCK_NB;
		
        if (!flock($f, $flags)) {
        	fclose($f);
        	return null;
        }
         
        ftruncate($f, 0);
        fprintf($f, "Locked at %s\n", date(DATE_RFC3339));
        fflush($f);
		
        return new Lock($f, $path);
	}
	
}

?>