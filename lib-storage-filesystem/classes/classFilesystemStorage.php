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

class FilesystemStorage {
	
	private $path = '';
	private $name = '';

	//************************************************************************************
	public function getPath() { return $this->path; }
	public function getName() { return $this->name; }
	
	//************************************************************************************
	public function __construct($name, $path) {
		$this->path = rtrim($path,'/') . '/';
		$this->name = $name;
	}
	
	//************************************************************************************
	private function prepareName($name) {
		$name = trim($name);
		$name = str_replace('..', '', $name);
		$name = str_replace('/', '', $name);
		$name = str_replace(' ', '_', $name);
		return $name;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 */
	public function removeContainer($name) {
		$name = $this->prepareName($name);
		if (!$name) throw new InvalidArgumentException('Empty name given');
		$path = $this->path . $name;
		
		UtilsIO::rmRecursive($path);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return FilesystemStorageContainer
	 */
	public function ensureContainer($name) {
		$name = $this->prepareName($name);
		if (!$name) throw new InvalidArgumentException('Empty name given');
		$path = $this->path . $name;
		
		if (!is_dir($path)) {
			$res = @mkdir($path, 0777);
			if (!$res) {
				throw new IOException(sprintf('Could not create directory %s', $path));
			}
		} 
		
		return new FilesystemStorageContainer($name, $path);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return FilesystemStorageContainer
	 */
	public function getContainer($name) {
		$name = $this->prepareName($name);
		if (!$name) throw new InvalidArgumentException('Empty name given');
		$path = $this->path . $name;
		
		if (is_dir($path)) {
			return new FilesystemStorageContainer($name, $path);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return boolean
	 */
	public function hasContainer($name) {
		$name = $this->prepareName($name);
		if (!$name) return false;
		$path = $this->path . $name;
		return is_dir($path);
	}
	
}


?>