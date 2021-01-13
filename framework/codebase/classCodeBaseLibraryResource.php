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


class CodeBaseLibraryResource {
	
	/**
	 * @var CodeBaseLibrary
	 */
	private $oLibrary = null;
	private $name = '';
	
	//************************************************************************************
	public function getName() { return $this->name; }
	
	//************************************************************************************
	/**
	 * @return CodeBaseLibrary
	 */
	public function getLibrary() { return $this->oLibrary; }
	
	//************************************************************************************
	public function __construct($oLibrary, $name) {
		$this->oLibrary = $oLibrary;
		$this->name = $name;
	}
	
	//************************************************************************************
	public function exists() {
		if ($this->oLibrary) {
			return $this->oLibrary->exists($this->name);
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	public function realPath() {
		if ($this->oLibrary) {
			return $this->oLibrary->realPath($this->name);
		} else {
			throw new IllegalStateException('Could not get realpath of empty resource');
		}
	}
	
	//************************************************************************************
	public function contents() {
		if ($this->exists()) {
			return file_get_contents($this->realPath());
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	public function size() {
		if ($this->exists()) {
			$info = stat($this->realPath());
			return $info['size'];
		} else {
			return 0;
		}
	}
	
	//************************************************************************************
	public function mtime() {
		if ($this->exists()) {
			$info = stat($this->realPath());
			return $info['mtime'];
		} else {
			return 0;
		}
	}

	//************************************************************************************
	public function parseJSON() {
		if ($this->exists()) {
			$res = json_decode($this->contents(), true);
			if ($res === null) {
				if (json_last_error() != JSON_ERROR_NONE) {
					throw new IOException(sprintf('Parsing %s failed - %s', $this->name, json_last_error_msg()));
				} 
			}
			return $res; 
		} else {
			return array();
		}
	}
	
}

?>