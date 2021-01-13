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


abstract class ORMTableRecordAdapter_LinkedTable extends ORMTableRecordAdapter {
	
	/**
	 * @var string
	 */
	private $linkedTableName = '';
	
	/**
	 * @var Closure
	 */
	private $oSelectorFunc = null;
	
	/**
	 * Wartosci przechowywane
	 * @var mixed[]
	 */
	private $values = array();
	
	/**
	 * Czy cos zostalo zmienione
	 * @var bool
	 */
	protected $changed = false;
	
	/**
	 * Czy zostalo zaladowane
	 * @var bool
	 */
	protected $loaded = false;
	
	//************************************************************************************
	public function wasLoaded() { return $this->loaded; }
	public function wasChanged() { return $this->changed; }
	
	//************************************************************************************
	/**
	 * @param ORMTableRecord $oRecord
	 * @param string $linkedTableName
	 * @param Closure $oSelectorFunc Funckja zwracajaca NameValuePair[] w roli selektora na polaczonej tabeli 
	 */
	public function __construct($oRecord, $linkedTableName, $oSelectorFunc) {
		parent::__construct($oRecord);
		if (!($oSelectorFunc instanceof Closure)) throw new InvalidArgumentException('oSelectorFunc is not Closure');
		
		$linkedTableName = trim($linkedTableName);
		if (!$linkedTableName) throw new InvalidArgumentException('linkedTableName is empty');
		
		$this->linkedTableName = $linkedTableName;
		$this->oSelectorFunc = $oSelectorFunc;
		
		if (true) {
			$self = $this;
			$oRecord->getEvents(ORMTableRecord::EVENTS_AFTER_ADD)->add(function($oRecord) use ($self) { $self->doSave(); });
			$oRecord->getEvents(ORMTableRecord::EVENTS_AFTER_UPDATE)->add(function($oRecord) use ($self) { $self->doSave(); });		
			$oRecord->getEvents(ORMTableRecord::EVENTS_AFTER_DELETE)->add(function($oRecord) use ($self) { $self->doDelete(); });
		}
	}
	
	//************************************************************************************
	/**
	 * @return NameValuePair[]
	 */
	protected function callSelector() {
		$func = $this->oSelectorFunc;
		$res = $func();		
		if (!$res || !is_array($res)) throw new IllegalStateException('SelectorFunc returned invalid value');
		foreach($res as $v) {
			if (!($v instanceof NameValuePair)) throw new IllegalStateException('SelectorFunc returned not NameValuePair[]');
		}
		return $res;
	}
	
	//************************************************************************************
	protected function getWhereSQL() {
		$sql = '(1 ';
		foreach($this->callSelector() as $e) {
			$sql .= sprintf(' AND %s.%s = %s ', $this->linkedTableName, $e->getName(), $this->getORM()->toSQL($e->getValue()));
		}
		$sql .= ')';
		
		return $sql;
	}
	
	//************************************************************************************
	public final function doLoad() {
		$this->values = array();
		$this->onBeforeLoad();
		
		$query = sprintf('SELECT * FROM %s WHERE %s',
			$this->linkedTableName,
			$this->getWhereSQL()
		);
		
		foreach($this->getRecord()->getSQLStorage()->getRecords($query) as $oRow) {
			$res = $this->internalParseValue($oRow);
			if ($res !== false) {
				$this->values[] = $res;
			}
		}
		
		$this->changed = false;
		$this->loaded = true;
		
		$this->onAfterLoad();
	}
	
	//************************************************************************************
	public final function doDelete() {
		try {
			$oTrans = $this->getSQLStorage()->beginTransaction();
			$oTrans->executeQuery(sprintf('DELETE FROM %s WHERE %s', $this->linkedTableName, $this->getWhereSQL()));
			$oTrans->commit();
			
			$this->values = array();
			$this->loaded = true;
			$this->changed = false;
		} catch(Exception $e) {
			if ($oTrans) $oTrans->rollback();
			throw $e;
		}
	}
	
	//************************************************************************************
	public final function doSave() {
		if (!$this->changed && !$this->loaded) return;
		
		if ($this->changed) {
			try {
				$oTrans = $this->getSQLStorage()->beginTransaction();
				$oTrans->executeQuery(sprintf('DELETE FROM %s WHERE %s', $this->linkedTableName, $this->getWhereSQL()));
				
				$SelectorArray = $this->callSelector();
				$this->onBeforeSave();
				
				foreach($this->internalGetSQLValues() as $row) {
					
					$fields = array();
					$values = array();
					
					foreach($SelectorArray as $oPair) {
						$fields[] = $oPair->getName();
						$values[] = $this->getORM()->toSQL($oPair->getValue());
					}
					foreach($row as $k => $v) {
						$fields[] = $k;
						$values[] = $this->getORM()->toSQL($v);
					}
					
					$oTrans->executeQuery(sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->linkedTableName, implode(',',$fields), implode(',',$values)));
				}
				
				$this->onAfterSave();
				$oTrans->commit();
				
			} catch(Exception $e) {
				if ($oTrans) $oTrans->rollback();
				throw $e;
			}
		}
		
		$this->changed = false;
		$this->loaded = true;
	}

	//************************************************************************************
	protected function onBeforeLoad() { }
	protected function onAfterLoad() { }
	protected function onBeforeSave() { }
	protected function onAfterSave() { }
	
	
	//************************************************************************************
	/**
	 * Zwraca wartosc ktora nalezy dodac do $this->values
	 * Jesli zwroci false to znaczy ze nic nie dodawac
	 * @param SQLResultsRow $oRow
	 * @return mixed
	 */
	protected abstract function internalParseValue($oRow);
	
	//************************************************************************************
	/**
	 * @return array[] Tablica wartosci do zapisania, kazda pozycja to tablica par k=>v
	 */
	protected abstract function internalGetSQLValues();
	
	//************************************************************************************
	protected function internalAddValue($oValue) {
		if (!$this->loaded) $this->doLoad();
		$this->values[] = $oValue;
		$this->changed = true;
	}

	//************************************************************************************
	protected function internalGetValues() {
		if (!$this->loaded) $this->doLoad();
		return $this->values;
	}
	
	//************************************************************************************
	protected function internalSetValues($arr) {
		$this->values = $arr;
		$this->changed = true;
		$this->loaded = true;
	}
	
	//************************************************************************************
	public function isEmpty() {
		if (!$this->loaded) $this->doLoad();
		return count($this->values) == 0;
	}
	
	//************************************************************************************
	public function getCount() {
		if (!$this->loaded) $this->doLoad();
		return count($this->values);
	}
	
	//************************************************************************************
	public function clear() {
		$this->internalSetValues(array());
	}
	
}

?>