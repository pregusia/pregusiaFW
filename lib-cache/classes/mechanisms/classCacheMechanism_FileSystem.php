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


class CacheMechanism_FileSystem implements ICacheMechanism {
	
	private $oContext = null;
	
	private $scope = '';
	private $pathBase = '';
	
	//************************************************************************************
	public function getApplicationContext() { return $this->oContext; }
	public function getScope() { return $this->scope; }
	
	
	//************************************************************************************
	private function __construct($oContext, $scope, $pathBase) {
		$this->oContext = $oContext;
		$this->scope = $scope;
		$this->pathBase = $pathBase;
	}	
	
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return bool
	 */
	public function delete($key) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		$path = $this->pathBase . $key;
		if (file_exists($path)) {
			@unlink($path);
			return true;
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function clear() {
		$num = 0;
		foreach(glob($this->pathBase . '*') as $filePath) {
			@unlink($filePath);
			$num += 1;
		}
		return $num;
	}
	
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param int $ttl
	 * @param Closure $oGenerator
	 */
	public function get($key, $ttl=0, $oGenerator=null) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		$path = $this->getPath($key, $ttl, $oGenerator);
		if ($path) {
			return file_get_contents($path);
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	public function getPath($key, $ttl=0, $oGenerator=null) {
		if (!$key) throw new InvalidArgumentException('key is empty');
		if ($oGenerator && !($oGenerator instanceof Closure)) throw new InvalidArgumentException('oGenerator is not Closure');
		
		$path = $this->pathBase . $key;
		
		if (file_exists($path)) {
			if ($ttl > 0) {
				$mtime = filemtime($path);
				if ($this->getApplicationContext()->getTimestamp() - $mtime > $ttl) {
					if ($oGenerator) {
						$content = $oGenerator();
						file_put_contents($path, $content);					
					} else {
						throw new IllegalStateException(sprintf('Requested item %s but no generator given', $key));
					}
				}
			}
		} else {
			if ($oGenerator) {
				$content = $oGenerator();
				file_put_contents($path, $content);			
			} else {
				throw new IllegalStateException(sprintf('Requested item %s but no generator given', $key));
			}
		}
		
		return $path;
	}	
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return bool
	 */
	public function contains($key, $ttl=0) {
		if (!$key) throw new InvalidArgumentException('key is empty');
		$path = $this->pathBase . $key;
		
		if (file_exists($path)) {
			if ($ttl > 0) {
				$mtime = filemtime($path);
				if ($this->getApplicationContext()->getTimestamp() - $mtime > $ttl) {
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param string $value
	 * @param int $ttl
	 */
	public function set($key, $value, $ttl=0) {
		if (!$key) throw new InvalidArgumentException('key is empty');
		$path = $this->pathBase . $key;

		file_put_contents($path, $value);
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param Configuration $oConfig
	 * @return CacheMechanism_FileSystem
	 */
	public static function Create($oContext, $oConfig) {
		if (!($oContext instanceof ApplicationContext)) throw new InvalidArgumentException('oContext is not ApplicationContext');
		if (!($oConfig instanceof Configuration)) throw new InvalidArgumentException('oConfig is not Configuration');
		
		$scope = $oConfig->getValue('scope');
		if (!$scope) throw new ConfigurationException('Entry "scope" is invalid in CacheMechanism_FileSystem config');

		if ($oConfig->hasKey('path')) {
			$path = $oConfig->getPath('path', true);
		} else {
			$path = $oContext->adaptPath('./cache/', true) . $scope . '/';
		}
		
		if (!is_dir($path)) mkdir($path,0777,true);
		
		return new self($oContext, $scope, $path);
	}
	
}

?>