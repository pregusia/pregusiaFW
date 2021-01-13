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

class FilesystemStorageContainer implements IFilesystemStorageContainer {
	
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
	/**
	 * @return string[]
	 */
	public function listFiles() {
		$arr = array();
		foreach(glob($this->path . '*') as $file) {
			if (is_file($file)) {
				$arr[] = basename($file);
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public static function prepareFileName($name) {
		$name = trim($name);
		$name = str_replace('..', '', $name);
		$name = str_replace('/', '', $name);
		$name = str_replace(' ', '_', $name);
		return $name;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function contains($name) {
		$name = self::prepareFileName($name);
		if (!$name) return false;
		$path = $this->path . $name;
		return is_file($path);
	}
	
	//************************************************************************************
	public function putContents($name, $contents) {
		$name = self::prepareFileName($name);
		if (!$name) throw new InvalidArgumentException('Empty name given');
		
		$res = file_put_contents($this->path . $name, $contents);
		if ($res === false) {
			throw new IOException(sprintf('Could not put contents to %s', $this->path . $name));
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public function getContents($name) {
		$name = self::prepareFileName($name);
		if (!$name) throw new InvalidArgumentException('Empty name given');
		
		$path = $this->path . $name;
		if (is_file($path)) {
			return file_get_contents($path);
		} else {
			throw new IOException(sprintf('Could not find file %s', $name));
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function remove($name) {
		$name = self::prepareFileName($name);
		if (!$name) throw new InvalidArgumentException('Empty name given');
		
		$path = $this->path . $name;
		if (is_file($path)) {
			$res = @unlink($path);
			if (!$res) {
				throw new IOException(sprintf('Could not remove %s', $path));
			}
			return true;
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	public function removeAll() {
		foreach($this->listFiles() as $file) {
			@unlink($this->path . $file);
		}
	}
	
}

?>