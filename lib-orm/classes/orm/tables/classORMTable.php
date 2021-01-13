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


abstract class ORMTable implements IEnumerable {

	protected $tableName = '';
	
	/**
	 * @var CodeBaseDeclaredClass
	 */
	protected $oRecordClass = null;
	
	/**
	 * @var ORMTableFieldDefinition[]
	 */
	protected $fields = array();	
	
	/**
	 * @var ORM
	 */
	protected $oORM = null;

	// #################################################################################
	// Getters

	//************************************************************************************
	public function getTableName() { return $this->tableName; }

	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredClass
	 */
	public function getRecordClass() { return $this->oRecordClass; }
	
	//************************************************************************************
	/**
	 * @return SQLStorage
	 */
	public function getSQLStorage() { return $this->getORM()->getSQLStorage(); }
	
	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() { return $this->getORM()->getComponent()->getApplicationContext(); }
	
	//************************************************************************************
	/**
	 * @return ORM
	 */
	public function getORM() { return $this->oORM; }

	//************************************************************************************
	/**
	 * @param ORM $oORM
	 */
	public function setORM($oORM) {
		if (!($oORM instanceof ORM)) throw new InvalidArgumentException('oORM is not ORM');
		$this->oORM = $oORM;
	}
	
	// #################################################################################
	// Table fields

	//************************************************************************************
	/**
	 * @param ORMTableFieldDefinition $oField
	 */
	protected function addField($oField) {
		if (!($oField instanceof ORMTableFieldDefinition)) throw new InvalidArgumentException('oField is not ORMTableFieldDefinition');
		if (isset($this->fields[$oField->getName()])) throw new ORMException(sprintf('Field with name %s already exists in table %s', $oField->getName(), $this->getTableName()));
		
		$this->fields[$oField->getName()] = $oField;
		
		$oField->setTable($this);
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableFieldDefinition
	 * @param string $name
	 */
	public function getFieldDefinition($name) {
		return $this->fields[$name];
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableFieldDefinition[]
	 */
	public function getFieldsDefinitions() {
		return $this->fields;
	}

	// #################################################################################
	// Enumerable
	
	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 * @return Enum
	 */
	public function getEnum($oSelect=null) {
		$oEnum = new Enum();
		foreach($this->getList($oSelect) as $oRecord) {
			false && $oRecord = new ORMTableRecord();
			$oEnum->add($oRecord->getPrimaryKeyField()->get(), $oRecord->getEnumCaption());
		}
		return $oEnum;
	}
	
	//************************************************************************************
	public function enumerableUsageType() {
		if ($this->getCount() > 50) {
			return IEnumerable::USAGE_SUGGEST;
		} else {
			return IEnumerable::USAGE_SIMPLE;
		}
	}
	
	//************************************************************************************
	public function enumerableGetAllEnum() {
		return $this->getEnum();
	}
	
	//************************************************************************************
	public function enumerableToString($param) {
		if (is_array($param)) {
			$oSelect = new ORMQuerySelect();
			$oSelect->addCondition('a_main.' . $this->getPrimaryKey()->get(0), ORMQueryConditionFactory::makeIn($param));
			$Items = $this->getList($oSelect, true);
			
			$res = array();
			foreach($param as $id) {
				$res[$id] = $Items[$id] ? $Items[$id]->getEnumCaption() : '';
			}
			return $res;
			
		} else {
			$oRecord = $this->getByPK($param);
			if ($oRecord) {
				return $oRecord->getEnumCaption();
			} else {
				return '';
			}
		}
	}
	
	//************************************************************************************
	/**
	 * Funkcja powinna zostac nadpisana w klasach potomnych w celach optymalizacyjnych
	 * @param unknown $text
	 */
	public function enumerableSuggest($text) {
		$oSelect = new ORMQuerySelect();
		$oEnum = new Enum();
		
		foreach($this->getList($oSelect) as $oRecord) {
			false && $oRecord = new ORMTableRecord();
			
			$caption = $oRecord->getEnumCaption();
			$val = $oRecord->getPrimaryKeyField()->get();
			
			if (strpos(strtolower($caption), strtolower($text)) !== false) {
				$oEnum->add($val, $caption);
			}
		}
		
		return $oEnum;		
	}
	
	// #################################################################################
	// Functions

	//************************************************************************************
	public function __construct() {
	}
	
	//************************************************************************************
	/**
	 * @return ORMRelationDefinition[]
	 */
	public function getRelations() {
		return $this->getORM()->getRelationsFor($this);
	}
	
	//************************************************************************************
	/**
	 * @param ORMKey $oKey
	 */
	public function isKeyUnique($oKey) {
		foreach($oKey->getFields() as $f) {
			$oDef = $this->getFieldDefinition($f);
			if (!$oDef) throw new ORMException(sprintf('Field %s not found in table %s', $f, $this->getTableName()));
			if (!$oDef->isUnique()) return false;
		}
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param ORMKey $oKey
	 */
	public function isKeyNull($oKey) {
		foreach($oKey->getFields() as $f) {
			$oDef = $this->getFieldDefinition($f);
			if (!$oDef) throw new ORMException(sprintf('Field %s not found in table %s', $f, $this->getTableName()));
			if ($oDef->isNullable()) return true;
		}
		return false;
	}	
	
	//************************************************************************************
	/**
	 * @return ORMKey
	 */
	public function getPrimaryKey() {
		$arr = array();
		foreach($this->getFieldsDefinitions() as $oDef) {
			false && $oDef = new DBFieldDefinition();
			if ($oDef->isPrimary()) {
				$arr[] = $oDef->getName();
			}
		}
		if ($arr) {
			return new ORMKey($arr);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 */
	public function createNew() {
		return $this->getRecordClass()->getInstance($this);
	}
	
	//************************************************************************************
	/**
	 * @param array $ids
	 * @param bool $useKeys
	 * @return ORMTableRecord[]
	 */
	public function getListIds($ids,$useKeys=false) {
		if (!is_array($ids)) return array();
		$oSelect = new ORMQuerySelect();
		$oSelect->addCondition('a_main.' . $this->getPrimaryKey()->get(0), ORMQueryConditionFactory::makeIn($ids));
		return $this->getList($oSelect,$useKeys);
	}
	
	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 * @return ORMTableRecord[]
	 */
	public function getList($oSelect=null,$useKeys=false) {
		$oSelect = ORMQuerySelect::Ensure($oSelect);
		$oSelect->setMainTable($this);
		
		$oResults = $this->getSQLStorage()->query($oSelect->createSelectQuery($this->getORM()), SQLStorage::QUERY_RETURN_RESULT_SET);
		$Records = array();
		
		while($oRow = $oResults->next()) {
			$oMappedRow = ORMResultsSetMappedRow::Create($this->getORM(), $oRow, $oSelect); 
			$oRecord = $oMappedRow->get('a_main');
			
			if ($useKeys) {
				$Records[$oRecord->getPrimaryKeyField()->get()] = $oRecord;
			} else {
				$Records[] = $oRecord;
			}
		}
		
		$oResults->close();
		return $Records;
	}
	
	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 * @return ORMTableRecord
	 */
	public function getFirst($oSelect=null) {
		$oSelect = ORMQuerySelect::Ensure($oSelect);
		$oSelect->setLimit(0, 1);
		
		$arr = $this->getList($oSelect);
		if ($arr[0]) return $arr[0];
		return null;
	}

	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 * @return int
	 */
	public function getCount($oSelect=null) {
		$oSelect = ORMQuerySelect::Ensure($oSelect);
		$oSelect->setMainTable($this);
		
		return intval($this->getSQLStorage()->getFirstColumn($oSelect->createCountQuery($this->getORM())));
	}
	
	//************************************************************************************
	public function pkExists($pk) {
		$query = sprintf('SELECT COUNT(*) FROM %s WHERE %s = %d',
			$this->getTableName(),
			$this->getPrimaryKey()->get(0),
			$pk
		);
		
		return intval($this->getSQLStorage()->getFirstColumn($query)) > 0;
	}	
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 */
	public function getByPK() {
		$args = func_get_args();
		
		$oSelect = new ORMQuerySelect();
		$oKey = $this->getPrimaryKey();
		
		for($i=0;$i<$oKey->getCount();++$i) {
			$val = $args[$i];
			$oSelect->addCondition(sprintf('a_main.%s', $oKey->get($i)), ORMQueryConditionFactory::makeEqual($val));
		}
		
		return $this->getFirst($oSelect);
	}
	
	//************************************************************************************
	/**
	 * @param mixed[] $pks
	 * @return ORMTableRecord[]
	 */
	public function getByPKs($pks) {
		if (!is_array($pks)) return array();
		
		$oKey = $this->getPrimaryKey();
		if ($oKey->getCount() != 1) throw new IllegalStateException(sprintf('Could not perform getByPKs with PK size %d', $oKey->getCount()));
		
		$oSelect = new ORMQuerySelect();
		$oSelect->addCondition(sprintf('a_main.%s', $oKey->get(0)), ORMQueryConditionFactory::makeIn($pks));
		return $this->getList($oSelect);
	}
	
	//************************************************************************************
	public function doTruncate() {
		$this->getSQLStorage()->query(sprintf('TRUNCATE TABLE `%s`', $this->getTableName()));
	}
	
	//************************************************************************************
	/**
	 * Usuwa podany rekord
	 * @param mixed $pk
	 */
	public function doDelete($pk, $useOrm=true) {
		if ($useOrm) {
			if (is_array($pk)) {
				if (empty($pk)) return;
			
				$oSelect = new ORMQuerySelect();
				$oSelect->addCondition($this->getPrimaryKey()->get(0), ORMQueryConditionFactory::makeIn($pk));
				foreach($this->getList($oSelect) as $oRecord) {
					$oRecord->doDelete();
				}
				
			} else {
				$oRecord = $this->getByPK($pk);
				if ($oRecord) {
					$oRecord->doDelete();
				}
			}
			
		} else {
			
			if (is_array($pk)) {
				if (empty($pk)) return;
				
				foreach($pk as &$v) $v = $this->getORM()->toSQL($v);
				
				$this->getSQLStorage()->query(sprintf('DELETE FROM `%s` WHERE `%s` IN (%s)',
					$this->getTableName(),
					$this->getPrimaryKey()->get(0),
					implode(', ', $pk)
				));
				
			} else {
				$this->getSQLStorage()->query(sprintf('DELETE FROM `%s` WHERE `%s` = %d',
					$this->getTableName(),
					$this->getPrimaryKey()->get(0),
					$pk
				));
			}
		}
	}

}

?>