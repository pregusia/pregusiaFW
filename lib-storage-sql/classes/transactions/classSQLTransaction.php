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


class SQLTransaction {
	
	const STATE_FAKE = 1;
	const STATE_REAL = 2;
	const STATE_USED = 3;
	
	/**
	 * @var SQLStorage
	 */
	private $oStorage = null;

	/**
	 * @var ISQLTransactionHook[]
	 */
	private $hooks = array();
	
	private $state = 0;
	
	//************************************************************************************
	/**
	 * @return SQLStorage
	 */	
	public function getStorage() { return $this->oStorage; }

	//************************************************************************************
	public function getState() { return $this->state; }
	
	//************************************************************************************
	public function __construct($oStorage) {
		if (!($oStorage instanceof SQLStorage)) throw new InvalidArgumentException('oStorage is not SQLStorage');
		$this->oStorage = $oStorage;
		
		if (!$this->getStorage()->getActiveTransaction()) {
			$this->getStorage()->setActiveTransaction($this);
			$this->getStorage()->getConnector()->autocommit(false);
			$this->state = self::STATE_REAL;
		} else {
			$this->state = self::STATE_FAKE;
		}
		
		$this->callHooks('onBegin');
	}
	
	//************************************************************************************
	/**
	 * Commituje tranzakcje
	 * @return boolean
	 */
	public function commit() {
		if ($this->state != self::STATE_REAL && $this->state != self::STATE_FAKE) return false;
		if (!$this->getStorage()->getActiveTransaction()) return false;
		if ($this->getStorage()->getActiveTransaction() == $this) {
			$this->getStorage()->setActiveTransaction(null);
		}
		
		$oCommitException = null;
		
		if ($this->state == self::STATE_REAL) {
			try {
				$this->getStorage()->getConnector()->commit();
			} catch(Exception $e) {
				$oCommitException = $e;
			} 
			try {
				$this->getStorage()->getConnector()->autocommit(true);
			} catch(Exception $e) {
			}
		}
			
		$this->state = self::STATE_USED;
		$this->callHooks('onCommit', $oCommitException);
		return true;
	}
	
	//************************************************************************************
	/**
	 * Wycofuje tranzakcje
	 * @return boolean
	 */
	public function rollback() {
		if ($this->state != self::STATE_REAL && $this->state != self::STATE_FAKE) return false;
		if (!$this->getStorage()->getActiveTransaction()) return false;
		if ($this->getStorage()->getActiveTransaction() == $this) {
			$this->getStorage()->setActiveTransaction(null);
		}
		
		if ($this->state == self::STATE_REAL) {
			try {
				$this->getStorage()->getConnector()->rollback();
			} catch(Exception $e) {
			} 
			try {
				$this->getStorage()->getConnector()->autocommit(true);
			} catch(Exception $e) {
			}
		}
			
		$this->state = self::STATE_USED;
		$this->callHooks('onRollback');
		return true;
	}
	
	//************************************************************************************
	public function executeQuery($query) {
		$this->getStorage()->query($query);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return ISQLTransactionHook
	 */
	public function getHook($name) {
		return $this->hooks[$name];
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param ISQLTransactionHook $oHook
	 * @throws InvalidArgumentException
	 */
	public function addHook($name, $oHook) {
		if (!($oHook instanceof ISQLTransactionHook)) throw new InvalidArgumentException('oHook is not ISQLTransactionHook');
		if ($this->getHook($name)) throw new InvalidArgumentException(sprintf('Hook %s already registered', $name));
		
		$this->hooks[$name] = $oHook;
	}
	
	//************************************************************************************
	private function callHooks($name, $arg=null) {
		foreach($this->hooks as $oHook) {
			false && $oHook = new ISQLTransactionHook();
			try {
				if ($name == 'onCommit') $oHook->onCommit($this, $arg);
				if ($name == 'onBegin') $oHook->onBegin($this);
				if ($name == 'onRollback') $oHook->onRollback($this);
			} catch(Exception $e) {
				Logger::warn(sprintf("SQLTransaction %s", $name) , $e);
			}
		}
	}
	
}

?>