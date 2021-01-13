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


class CacheMechanism_Memcached implements ICacheMechanism {
	
	private $oContext = null;
	private $scope = '';
	private $oMemcached = null;
	private $defaultTTL = 0;
	
	//************************************************************************************
	public function getApplicationContext() { return $this->oContext; }
	public function getScope() { return $this->scope; }
	
	
	//************************************************************************************
	public function __construct($oContext, $scope, $oMemcached, $defaultTTL) {
		if (!($oContext instanceof ApplicationContext)) throw new InvalidArgumentException('oContext is not ApplicationContext');
		
		$this->oContext = $oContext;
		$this->scope = $scope;
		$this->oMemcached = $oMemcached;
		$this->defaultTTL = $defaultTTL;
	}	
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return bool
	 */
	public function delete($key) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		$this->oMemcached->delete(sprintf('%s_%s', $this->scope, $key));
		return true;
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function clear() {
		return 0;
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
		
		if ($ttl <= 0) $ttl = $this->defaultTTL;
		
		$memKey = sprintf('%s_%s', $this->scope, $key);
		$value = $this->oMemcached->get($memKey);
		$resultCode = $this->oMemcached->getResultCode();

		if ($resultCode == Memcached::RES_SUCCESS) {
			return $value;
		}
		elseif ($resultCode = Memcached::RES_NOTFOUND) {
			// nie znaleziono w cache
			if (!$oGenerator) throw new IllegalStateException(sprintf('Requested item %s but no generator given', $key));
			
			$value = $oGenerator();
			$this->oMemcached->set($memKey, $value, $ttl);
			return $value;
		}
		else {
			throw new IOException(sprintf("Got memcached result code %d (%s)", $resultCode, $this->oMemcached->getResultMessage()));
		} 
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return bool
	 */
	public function contains($key, $ttl=0) {
		if (!$key) throw new InvalidArgumentException('key is empty');
		$memKey = sprintf('%s_%s', $this->scope, $key);

		if ($ttl <= 0) $ttl = $this->defaultTTL;
		
		$this->oMemcached->get($memKey);
		$resultCode = $this->oMemcached->getResultCode();
		
		if ($resultCode == Memcached::RES_SUCCESS) {
			return true;
		}
		elseif ($resultCode == Memcached::RES_NOTFOUND) {
			return false;
		}
		else {
			throw new IOException(sprintf("Got memcached result code %d (%s)", $resultCode, $this->oMemcached->getResultMessage()));
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
		$memKey = sprintf('%s_%s', $this->scope, $key);
		if ($ttl <= 0) $ttl = $this->defaultTTL;
		
		$this->oMemcached->set($memKey, $value, $ttl);
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
		
		$servers = $oConfig->getArray('servers');
		if (!$servers) throw new ConfigurationException('Entry "servers" is empty in CacheMechanism_RuntimeMemory config');
		
		$ttl = intval($oConfig->getValue('ttl'));
		if ($ttl <= 0) $ttl = 3600;
		
		
		$oMemcached = new Memcached();
		foreach($servers as $serverSpec) {
			$host = '';
			$port = 11211;
			
			if (strpos($serverSpec, ':') !== false) {
				$tmp = explode(':',$serverSpec,2);
				$host = $tmp[0];
				$port = intval($tmp[1]);
			} else {
				$host = $serverSpec;
			}
			
			$oMemcached->addserver($host, $port);
		}
		
		
		return new self($oContext, $scope, $oMemcached, $ttl);
	}
	
}

?>