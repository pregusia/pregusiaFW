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


class CacheMechanism_RuntimeMemory implements ICacheMechanism {
	
	private $oContext = null;
	private $scope = '';
	private $items = array();
	
	//************************************************************************************
	public function getApplicationContext() { return $this->oContext; }
	public function getScope() { return $this->scope; }
	
	
	//************************************************************************************
	public function __construct($oContext, $scope) {
		if (!($oContext instanceof ApplicationContext)) throw new InvalidArgumentException('oContext is not ApplicationContext');
		
		$this->oContext = $oContext;
		$this->scope = $scope;
	}	
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return bool
	 */
	public function delete($key) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		unset($this->items[$key]);
		return true;
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function clear() {
		$res = count($this->items);
		$this->items = array();
		return $res;
	}
	
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param int $ttl
	 * @param Closure $oGenerator
	 * @return string
	 */
	public function get($key, $ttl=0, $oGenerator=null) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		if ($oGenerator && !($oGenerator instanceof Closure)) throw new InvalidArgumentException('oGenerator is not Closure');
		
		if (isset($this->items[$key])) {
			return $this->items[$key];
		}
		
		if ($oGenerator) {
			$this->items[$key] = $oGenerator();
			return $this->items[$key];
		} else {
			throw new IllegalStateException(sprintf('Requested item %s but no generator given', $key));
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return bool
	 */
	public function contains($key, $ttl=0) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		return isset($this->items[$key]);
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param string $value
	 * @param int $ttl
	 */
	public function set($key, $value, $ttl=0) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		$this->items[$key] = $value;
	}	
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param Configuration $oConfig
	 * @return CacheMechanism_RuntimeMemory
	 */
	public static function Create($oContext, $oConfig) {
		if (!($oContext instanceof ApplicationContext)) throw new InvalidArgumentException('oContext is not ApplicationContext');
		if (!($oConfig instanceof Configuration)) throw new InvalidArgumentException('oConfig is not Configuration');
		
		$scope = $oConfig->getValue('scope');
		if (!$scope) throw new ConfigurationException('Entry "scope" is invalid in CacheMechanism_RuntimeMemory config');
		
		return new self($oContext, $scope);
	}
	
}

?>