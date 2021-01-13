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

/**
 * @author pregusia
 * @NeedLibrary lib-storage-sql
 *
 */
class CacheMechanism_SQLStorage implements ICacheMechanism {
	
	private $oContext = null;
	private $scope = '';
	private $oStorage = null;
	private $defaultTTL = 0;
	
	//************************************************************************************
	public function getApplicationContext() { return $this->oContext; }
	public function getScope() { return $this->scope; }
	
	//************************************************************************************
	/**
	 * @return SQLStorage
	 */
	public function getStorage() { return $this->oStorage; }
	
	
	//************************************************************************************
	private function __construct($oContext, $oStorage, $scope, $defaultTTL) {
		$this->oContext = $oContext;
		$this->oStorage = $oStorage;
		$this->scope = $scope;
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
		
		$this->getStorage()->query(sprintf('DELETE FROM sql_cache_entries WHERE scope="%s" AND name="%s"',
			$this->getStorage()->escapeString($this->scope),
			$this->getStorage()->escapeString($key)
		));
		return true;
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function clear() {
		$num = $this->getStorage()->getFirstColumn(sprintf('SELECT COUNT(*) FROM sql_cache_entries WHERE scope="%s"',
			$this->getStorage()->escapeString($this->scope)
		));
		
		$this->getStorage()->query(sprintf('DELETE FROM sql_cache_entries WHERE scope="%s"',
			$this->getStorage()->escapeString($this->scope)
		));

		return $num;
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
		
		if ($ttl <= 0) {
			$ttl = $this->defaultTTL;
		}

		$now = $this->getApplicationContext()->getTimestamp();
		$oRow = $this->getStorage()->getFirstRow(sprintf('SELECT * FROM sql_cache_entries WHERE scope="%s" AND name="%s"',
			$this->getStorage()->escapeString($this->scope),
			$this->getStorage()->escapeString($key)
		));

		if ($oRow && $oRow->getColumn('validUntil')->getValueMapped() >= $now) {
			return $oRow->getColumn('value')->getValueMapped();
		}
		
		// trzeba wygenerowac
		if (!$oGenerator) throw new IllegalStateException(sprintf('Requested item %s but no generator given', $name));
		$value = $oGenerator();
		
		try {
			$oTrans = $this->getStorage()->beginTransaction();
			
			$this->delete($key);
			$this->getStorage()->insertRecord('sql_cache_entries', array(
				'scope' => $this->scope,
				'name' => $key,
				'value' => $value,
				'validUntil' => $now + $ttl	
			));
			
			$oTrans->commit();
		} catch(Exception $e) {
			if ($oTrans) $oTrans->rollback();
			throw $e;
		}
		
		return $value;
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param int $ttl
	 * @return bool
	 */
	public function contains($key, $ttl=0) {
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		if ($ttl <= 0) {
			$ttl = $this->defaultTTL;
		}
		
		$now = $this->getApplicationContext()->getTimestamp();
		$oRow = $this->getStorage()->getFirstRow(sprintf('SELECT validUntil FROM sql_cache_entries WHERE scope="%s" AND name="%s"',
			$this->getStorage()->escapeString($this->scope),
			$this->getStorage()->escapeString($key)
		));

		if ($oRow && $oRow->getColumn('validUntil')->getValueMapped() >= $now) {
			return true;
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
		$key = trim($key);
		if (!$key) throw new InvalidArgumentException('key is empty');
		
		if ($ttl <= 0) {
			$ttl = $this->defaultTTL;
		}

		$now = $this->getApplicationContext()->getTimestamp();
		
		try {
			$oTrans = $this->getStorage()->beginTransaction();
			
			$this->delete($key);
			$this->getStorage()->insertRecord('sql_cache_entries', array(
				'scope' => $this->scope,
				'name' => $key,
				'value' => $value,
				'validUntil' => $now + $ttl	
			));
			
			$oTrans->commit();
		} catch(Exception $e) {
			if ($oTrans) $oTrans->rollback();
			throw $e;
		}	
	}
	
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param Configuration $oConfig
	 * @return CacheMechanism_SQLStorage
	 */
	public static function Create($oContext, $oConfig) {
		if (!($oContext instanceof ApplicationContext)) throw new InvalidArgumentException('oContext is not ApplicationContext');
		if (!($oConfig instanceof Configuration)) throw new InvalidArgumentException('oConfig is not Configuration');
		
		$scope = $oConfig->getValue('scope');
		if (!$scope) throw new ConfigurationException('Entry "scope" is invalid in CacheMechanism_SQLStorage config');

		$storageName = $oConfig->getValue('storageName');
		if (!$storageName) throw new ConfigurationException('Entry "storageName" is invalid in CacheMechanism_SQLStorage config');
		
		$ttl = intval($oConfig->getValue('ttl'));
		if ($ttl <= 0) $ttl = 3600;
		
		$oStorageComponent = $oContext->getComponent('storage.sql');
		false && $oStorageComponent = new SQLStorageApplicationComponent();
		
		$oSQLStorage = $oStorageComponent->getStorage($storageName);
		if (!$oSQLStorage) throw new ConfigurationException(sprintf('SQLStorage %s not found', $storageName));
		
		return new self($oContext, $oSQLStorage, $scope, $ttl);
	}
	
}

?>