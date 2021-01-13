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


class SQLStorage implements ISQLValueEscaper {
	
	const QUERY_RETURN_NONE = 0;
	const QUERY_RETURN_RESULT_SET = 1;
	const QUERY_RETURN_LAST_INSERT_ID = 2;
	const QUERY_RETURN_AFFECTED_ROWS = 3;
	
	/**
	 * @var SQLStorageApplicationComponent
	 */
	private $oComponent = null;
	
	private $name = '';
	private $config = array();
	
	/**
	 * @var ISQLConnector
	 */
	private $oConnectorInstance = false;
	
	/**
	 * @var CodeBaseDeclaredClass
	 */
	private $oConnectorClass = null;
	
	/**
	 * @var SQLTransaction
	 */
	private $oActiveTransaction = null;
	
	//************************************************************************************
	public function getName() { return $this->name; }
	
	//************************************************************************************
	/**
	 * @return SQLStorageApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	/**
	 * @return SQLTypesMapper
	 */
	public function getTypesMapper() { return $this->getComponent()->getTypesMapper(); } 
	
	//************************************************************************************
	private function __construct($oComponent, $name, $oConnectorClass, $config) {
		if (!($oComponent instanceof SQLStorageApplicationComponent)) throw new InvalidArgumentException('oComponent is not SQLStorageApplicationComponent');
		if (!($oConnectorClass instanceof CodeBaseDeclaredClass)) throw new InvalidArgumentException('oConnectorClass is not CodeBaseDeclaredClass');
		if (!is_array($config)) throw new InvalidArgumentException('config is not array');
		$this->oComponent = $oComponent;
		$this->oConnectorClass = $oConnectorClass;
		$this->config = $config;
		$this->name = $name;
	}
	
	//************************************************************************************
	/**
	 * @return ISQLConnector
	 */
	public function getConnector() {
		if ($this->oConnectorInstance === false) {
			$this->oConnectorInstance = $this->oConnectorClass->getInstance();
			$this->oConnectorInstance->start($this->config);
		}
		return $this->oConnectorInstance;
	}
	
	//************************************************************************************
	public function escapeString($val) {
		return $this->getConnector()->escapeString($val);
	}
	
	//************************************************************************************
	public function escapeBinary($val) {
		return $this->getConnector()->escapeBinary($val);
	}
	
	//************************************************************************************
	/**
	 * @param string $query
	 * @param int $returnType
	 * @return ISQLResultsSet
	 * @return int
	 */
	public function query($query, $returnType=0) {
		$query = trim($query);
		if (!$query) throw new InvalidArgumentException('Empty query');
		
		$oConnector = $this->getConnector();
		
		$oResults = $oConnector->query($query);
		
		if ($returnType == self::QUERY_RETURN_RESULT_SET) {
			return $oResults;
		}
		if ($returnType == self::QUERY_RETURN_LAST_INSERT_ID) {
			$oResults->close();
			return $oConnector->getLastInsertID();
		}
		if ($returnType == self::QUERY_RETURN_AFFECTED_ROWS) {
			$oResults->close();
			return $oConnector->getAffectedRows();
		}		
		
		$oResults->close();
		return 0;
	}

	
	
	//************************************************************************************
	public function insertRecord($tableName, $fields) {
		$tableName = trim($tableName);
		if (!$tableName) throw new InvalidArgumentException('Empty tableName');
		if (!is_array($fields)) throw new InvalidArgumentException('fields is not array');
		
		$query = sprintf('INSERT INTO `%s` (', $tableName);
		
		foreach($fields as $k => $v) {
			$query .= sprintf('`%s`,', $k);
		}
		
		$query = rtrim($query,',');
		$query .= sprintf(') VALUES (');
		
		foreach($fields as $k => $v) {
			$query .= sprintf('%s,', $this->getTypesMapper()->toSQL($v, $this));
		}
		
		$query = rtrim($query,',');
		$query .= ')';
		
		return $this->query($query, self::QUERY_RETURN_LAST_INSERT_ID);
	}

	//************************************************************************************
	public function replaceRecord($tableName, $fields) {
		$tableName = trim($tableName);
		if (!$tableName) throw new InvalidArgumentException('Empty tableName');
		if (!is_array($fields)) throw new InvalidArgumentException('fields is not array');
		
		$query = sprintf('REPLACE INTO `%s` (', $tableName);
		
		foreach($fields as $k => $v) {
			$query .= sprintf('`%s`,', $k);
		}
		
		$query = rtrim($query,',');
		$query .= sprintf(') VALUES (');
		
		foreach($fields as $k => $v) {
			$query .= sprintf('%s,', $this->getTypesMapper()->toSQL($v, $this));
		}
		
		$query = rtrim($query,',');
		$query .= ')';
		
		return $this->query($query, self::QUERY_RETURN_LAST_INSERT_ID);
	}

	//************************************************************************************
	public function updateRecord($tableName, $fields, $whereSQL) {
		$tableName = trim($tableName);
		$whereSQL = trim($whereSQL);
		if (!$tableName) throw new InvalidArgumentException('Empty tableName');
		if (!$whereSQL) throw new InvalidArgumentException('Empty whereSQL');
		if (!is_array($fields)) throw new InvalidArgumentException('fields is not array');
		if (empty($fields)) return 0;
		
		$query = sprintf('UPDATE `%s` SET ', $tableName);
		foreach($fields as $k => $v) {
			$query .= sprintf(' `%s` = %s, ', $k, $this->getTypesMapper()->toSQL($v, $this));
		}
		$query = rtrim($query,', ');
		$query .= sprintf(' WHERE %s', $whereSQL);
		
		return $this->query($query, self::QUERY_RETURN_AFFECTED_ROWS);
	}

	//************************************************************************************
	/**
	 * @param string $query
	 * @return SQLResultsRow[]
	 */
	public function getRecords($query) {
		$oResults = $this->query($query, self::QUERY_RETURN_RESULT_SET);
		$res = array();
		
		while($oRow = $oResults->next()) {
			$res[] = $oRow;
		}
		
		$oResults->close();
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param string $query
	 * @return SQLResultsRow
	 */
	public function getFirstRow($query) {
		$oResults = $this->query($query, self::QUERY_RETURN_RESULT_SET);
		$oRow = $oResults->next();
		$oResults->close();
		return $oRow;
	}
	
	//************************************************************************************
	/**
	 * Zwraca zmapowana wartosc pierwszej kolumny pierwszego wiersza wynikow zapytania
	 * @param string $query
	 * @return mixed
	 */
	public function getFirstColumn($query) {
		$oRow = $this->getFirstRow($query);
		if ($oRow) {
			$oColumn = $oRow->getFirstColumn();
			if ($oColumn) {
				return $oColumn->getValueMapped();
			}
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * Zwraca zmapowana wartosc kolumny o podanej nazwie/indexie z pierwszego wiersza wynikow podanego zapytania
	 * @param string $query
	 * @param string $columnRef
	 * @return mixed
	 */
	public function getColumn($query, $columnRef) {
		$columnRef = strval(trim($columnRef));
		if (!$columnRef) throw new InvalidArgumentException('columnRef is empty');
		
		$oRow = $this->getFirstRow($query);
		if ($oRow) {
			$oColumn = null;
			if (ctype_digit($columnRef)) {
				$oColumn = $oRow->getColumnByIndex(intval($columnRef));
			} else {
				$oColumn = $oRow->getColumnByName($columnRef);
			}
			
			if ($oColumn) {
				return $oColumn->getValueMapped();
			}
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * Zwraca zmapowane wartosci pierwszej kolumny z kazdego wiersza wynikow zapytania 
	 * @param string $query
	 * @return mixed[]
	 */
	public function getAllFirstColumn($query) {
		$res = array();
		$oResults = $this->query($query, self::QUERY_RETURN_RESULT_SET);
		
		while($oRow = $oResults->next()) {
			if ($oRow->getFirstColumn()) {
				$res[] = $oRow->getFirstColumn()->getValueMapped();
			}
		}
		
		$oResults->close();
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @return SQLTransaction
	 */
	public function getActiveTransaction() { return $this->oActiveTransaction; }
	
	//************************************************************************************
	public function setActiveTransaction($oTransaction) {
		if ($oTransaction) {
			if (!($oTransaction instanceof SQLTransaction)) throw new InvalidArgumentException('oTransaction is not SQLTransaction');
			$this->oActiveTransaction = $oTransaction;
		} else {
			$this->oActiveTransaction = null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return SQLTransaction
	 */
	public function beginTransaction() {
		return new SQLTransaction($this);
	}
	
	//************************************************************************************
	private $tablesCache = false;
	public function tableExists($tableName) {
		$tableName = trim($tableName);
		if (!$tableName) return false;
		
		if ($this->tablesCache === false) {
			$this->tablesCache = $this->getAllFirstColumn('SHOW TABLES');
		}
		
		return in_array($name, $this->tablesCache);
	}
	
	
	//************************************************************************************
	/**
	 * @param SQLStorageApplicationComponent $oComponent
	 * @param array $config
	 * @return SQLStorage
	 */
	public static function FromConfig($oComponent, $name, $config) {
		if (!($oComponent instanceof SQLStorageApplicationComponent)) throw new InvalidArgumentException('oComponent is not SQLStorageApplicationComponent');
		if (!is_array($config)) throw new InvalidArgumentException('config is not array');
		
		$oClass = CodeBase::getClass($config['connectorClass'], true);
		return new SQLStorage($oComponent, $name, $oClass, $config);
	}
}

?>