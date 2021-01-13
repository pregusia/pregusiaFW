<?php

class ORMResultsSetMappedRow {
	
	/**
	 * @var ORMQuerySelect
	 */
	private $oSelect = null;
	
	/**
	 * Mapowania nazwa aliasu => rekord
	 * @var ORMTableRecord[]
	 */
	private $records = array();
	
	/**
	 * Pola bez przypisanych rekordow
	 */
	private $generalFields = array();
	
	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 */
	private function __construct($oSelect) {
		if (!($oSelect instanceof ORMQuerySelect)) throw new InvalidArgumentException('Select is not ORMQuerySelect');
		$this->oSelect = $oSelect;
		
		foreach($oSelect->getKnownRecords() as $obj) {
			$this->records[$obj->getRelationDefinition()->getName()] = $obj->getRecord();
		}
	}
	
	//************************************************************************************
	public function getGeneralFieldValue($name) {
		return $this->generalFields[$name];
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 * @param SQLResultsColumn $oColumn
	 * @param ORM $oORM
	 */
	private function createRecord($oORM, $oColumn) {
		if (!($oColumn instanceof SQLResultsColumn)) throw new InvalidArgumentException('oColumn is not SQLResultsColumn');
		if (!($oORM instanceof ORM)) throw new InvalidArgumentException('oORM is not ORM');
		
		if (!$oColumn->getTableName()) return null;
		
		if (!$this->records[$oColumn->getTableName()->getName()]) {
			$oTable = $oORM->getTable($oColumn->getTableName()->getOrginal());
			if (!$oTable) throw new IllegalStateException(sprintf('Table %s not found', $oColumn->getTableName()->getOrginal()));
			
			$oRecord = $oTable->createNew();
			$oRecord->setMappedRow($this);
			
			$this->records[$oColumn->getTableName()->getName()] = $oRecord; 
		}
		
		return $this->records[$oColumn->getTableName()->getName()];
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 * @param string $aliasName
	 */
	public function get($aliasName) {
		if ($this->records[$aliasName]) {
			return $this->records[$aliasName];
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param SQLResultsColumn $oColumn
	 * @param ORM $oORM
	 */
	private function processColumn($oORM, $oColumn) {
		if (!($oColumn instanceof SQLResultsColumn)) throw new InvalidArgumentException('oColumn is not SQLResultsColumn');
		if (!($oORM instanceof ORM)) throw new InvalidArgumentException('oORM is not ORM');
		
		if ($oColumn->getTableName()) {
			$oRecord = $this->createRecord($oORM, $oColumn);
			if ($oRecord) {
				$oRecord->internalSetField($oColumn->getColumnName()->getOrginal(), $oColumn->getValueMapped());
			}
		} else {
			$this->generalFields[$oColumn->getColumnName()->getName()] = $oColumn->getValueMapped();
			$this->generalFields[$oColumn->getColumnName()->getOrginal()] = $oColumn->getValueMapped();
		}
	}
	
	//************************************************************************************
	/**
	 * Zwraca instancje relacji odpowiadajacej temu aliasowi
	 * @param string $alias
	 * @return ORMTableRecordRelationInstance
	 */
	private function getRelation($alias) {
		$arr = explode('$',$alias);
		if (count($arr) == 1) return null;
		
		$param = null;
		while($arr) {
			$name = array_shift($arr);
			
			if ($param === null) {
				$param = $this->records[$name];
				if (!$param) return null;
			}
			elseif ($param instanceof ORMTableRecordRelationInstance) {
				$oRecord = $param->getRecord(false);
				if ($oRecord) {
					try {
						$param = $oRecord->getRelation($name); // TODO: exception
					} catch(Exception $e) {
						return null;
					}
				} else {
					return null;
				}
			}
			elseif ($param instanceof ORMTableRecord) {
				try {
					$param = $param->getRelation($name); // TODO: exception
				} catch(Exception $e) {
					return null;
				}
			}
		}
		return $param;
	}
	
	//************************************************************************************
	private function fillRelations() {
		$arr = array();
		foreach($this->records as $alias => $oRecord) {
			false && $oRecord = new ORMTableRecord();
			if (strpos($alias, '$') === false) continue;
			$arr[] = array(
				'alias' => $alias,
				'record' => $oRecord
			);
		}
		
		$n = 0;
		while($n < 20 && $arr) {
			$tmp = array_shift($arr);
			$oRelation = $this->getRelation($tmp['alias']);
			if ($oRelation) {
				$oRelation->internalSetRecord($tmp['record']);
				
				try {
					$oRelation2 = $tmp['record']->getRelation($oRelation->getName());
					$oRelation2->internalSetRecord($oRelation->getLocalRecord());
				} catch(Exception $e) {
					
				}
				
			} else {
				$arr[] = $tmp;
			}
			
			$n += 1;
		}
		
	}
	
	//************************************************************************************
	private function callEvents() {
		foreach($this->records as $oRecord) {
			false && $oRecord = new ORMTableRecord();
			$oRecord->getEvents(ORMTableRecord::EVENTS_AFTER_LOAD)->call($oRecord);
			foreach($oRecord->getORM()->getComponent()->getORMExtensions() as $oExtension) {
				$oExtension->onAfterLoad($oRecord->getORM(), $oRecord);
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param SQLResultsRow $oRow
	 * @param ORMQuerySelect $oSelect
	 * @param ORM $oORM
	 * @return ORMResultsSetMappedRow
	 */
	public static function Create($oORM, $oRow, $oSelect) {
		if (!($oORM instanceof ORM)) throw new InvalidArgumentException('oORM is not ORM');
		if (!($oRow instanceof SQLResultsRow)) throw new InvalidArgumentException('oRow is not SQLResultsRow');
		if (!($oSelect instanceof ORMQuerySelect)) throw new InvalidArgumentException('oSelect is not ORMQuerySelect');

		$oMappedRow = new ORMResultsSetMappedRow($oSelect);
		
		foreach($oRow->getColumns() as $oColumn) {
			$oMappedRow->processColumn($oORM, $oColumn);
		}
		
		$oMappedRow->fillRelations();
		$oMappedRow->callEvents();
		
		return $oMappedRow;
	}
	
}

?>