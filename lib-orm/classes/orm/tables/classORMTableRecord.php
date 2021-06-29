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


abstract class ORMTableRecord {
	
	/**
	 * @var ORMTable
	 */
	private $oTable = null;
	
	/**
	 * @var ORMField[]
	 */
	private $fields = null;
	
	/**
	 * @var mixed[]
	 */
	private $customFields = array();
	
	/**
	 * Mapowania nazwa => DBRelationInstance
	 * @var ORMTableRecordRelationInstance[]
	 */
	private $relations = array();
	
	/**
	 * Wiersz z ktorego zostal stworzony ten rekord
	 * @var ORMResultsSetMappedRow
	 */
	private $mappedRow = null;
	
	private $eventsBeforeAdd = null;
	private $eventsAfterAdd = null;
	private $eventsBeforeUpdate = null;
	private $eventsAfterUpdate = null;
	private $eventsBeforeDelete = null;
	private $eventsAfterDelete = null;
	private $eventsAfterLoad = null;
	private $eventsTplRender = null;
	
	const EVENTS_BEFORE_ADD = 1;
	const EVENTS_BEFORE_DELETE = 2;
	const EVENTS_BEFORE_UPDATE = 3;
	const EVENTS_AFTER_ADD = 4;
	const EVENTS_AFTER_DELETE = 5;
	const EVENTS_AFTER_UPDATE = 6;
	const EVENTS_AFTER_LOAD = 7;
	const EVENTS_TPL_RENDER = 8;
	
	//************************************************************************************
	/**
	 * @return Events
	 */
	public function getEvents($type) {
		switch($type) {
			case self::EVENTS_AFTER_ADD: return $this->eventsAfterAdd;
			case self::EVENTS_AFTER_DELETE: return $this->eventsAfterDelete;
			case self::EVENTS_AFTER_UPDATE: return $this->eventsAfterUpdate;
			case self::EVENTS_BEFORE_ADD: return $this->eventsBeforeAdd;
			case self::EVENTS_BEFORE_DELETE: return $this->eventsBeforeDelete;
			case self::EVENTS_BEFORE_UPDATE: return $this->eventsBeforeUpdate;
			case self::EVENTS_AFTER_LOAD: return $this->eventsAfterLoad;
			case self::EVENTS_TPL_RENDER: return $this->eventsTplRender;
			default: return null;
		}
	}

	//************************************************************************************
	/**
	 * @return ORMResultsSetMappedRow
	 */
	public function getMappedRow() { return $this->mappedRow; }
	public function setMappedRow($v) { $this->mappedRow = $v; return $this; }
	
	//************************************************************************************
	public function getGeneralFieldValue($name) {
		if ($this->getMappedRow()) {
			return $this->getMappedRow()->getGeneralFieldValue($name);
		}
		return '';
	}
	
	//************************************************************************************
	public function setCustomField($name, $value) {
		$this->customFields[$name] = $value;
	}
	
	//************************************************************************************
	public function getCustomField($name) {
		return $this->customFields[$name];
	}
	
	//************************************************************************************
	/**
	 * @return ORMTable
	 */
	public function getTable() { return $this->oTable; }
	
	//************************************************************************************
	/**
	 * @return ORM
	 */
	public function getORM() { return $this->getTable()->getORM(); }
	
	//************************************************************************************
	/**
	 * @return SQLStorage
	 */
	public function getSQLStorage() { return $this->getTable()->getSQLStorage(); }

	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() { return $this->getTable()->getApplicationContext(); }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return ORMField
	 */
	public function getField($name) { return $this->fields[$name]; }
	
	//************************************************************************************
	/**
	 * @return ORMField[]
	 */
	public function getFields() { return $this->fields; }

	//************************************************************************************
	/**
	 * @return ORMTableRecordRelationInstance[]
	 */
	public function getRelations() { return $this->relations; }
	
	//************************************************************************************
	/**
	 * @param ORMTable $oTable
	 */
	public function __construct($oTable) {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		
		$this->oTable = $oTable;
		
		$this->eventsAfterAdd = new Events();
		$this->eventsAfterDelete = new Events();
		$this->eventsAfterUpdate = new Events();
		$this->eventsBeforeAdd = new Events();
		$this->eventsBeforeDelete = new Events();
		$this->eventsBeforeUpdate = new Events();
		$this->eventsAfterLoad = new Events();
		$this->eventsTplRender = new Events();

		// relacje
		foreach($oTable->getRelations() as $oDef) {
			false && $oDef = new ORMRelationDefinition();
			$this->relations[$oDef->getName()] = $oDef->createRelation($this);
		}
		
		// events
		foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
			$oExtension->onAfterCreated($this->getORM(), $this);
		}
	}
	
	//************************************************************************************
	public function internalSetField($name, $value) {
		if (!$this->fields[$name]) {
			$oDef = $this->getTable()->getFieldDefinition($name);
			if (!$oDef) {
				//throw new Exception("Field definition '$name' not found");
				return;
			}
			
			$oField = $oDef->createField();
			$oField->setRecord($this);
			
			$this->fields[$name] = $oField;
		}
		$this->getField($name)->load($value);
	}
	
	//************************************************************************************
	public function internalInitFields() {
		$this->fields = array();
		foreach($this->getTable()->getFieldsDefinitions() as $oDef) {
			$oField = $oDef->createField();
			$oField->setRecord($this);
			$this->fields[$oDef->getName()] = $oField; 
		}
		
		if ($oField = $this->getField('creationTime')) {
			$oField->set(date("Y-m-d H:i:s"));
		}
	}
	
	//************************************************************************************
	/**
	 * Ustawia wszystkie pola bazujac na wzorcu
	 * @param ORMTableRecord $oFrom
	 */
	public function copyFields($oFrom) {
		if (!($oFrom instanceof ORMTableRecord)) throw new InvalidArgumentException('oFrom is not ORMTableRecord');
		if ($oFrom->getTable() != $this->getTable()) throw new InvalidArgumentException('oFrom has not the same table as this');
		
		foreach($this->getTable()->getFieldsDefinitions() as $oDef) {
			$this->getField($oDef->getName())->set($oFrom->getField($oDef->getName())->get());
		}
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function wasAnyFieldChanged() {
		foreach($this->getFields() as $oField) {
			if ($oField->isChanged()) return true;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * Tworzy klon
	 * @return self
	 */
	public function createClone() {
		$oRecord = $this->getTable()->createNew();
		$oRecord->copyFields($this);
		$oRecord->getPrimaryKeyField()->set(0);
		return $oRecord;
	}
	
	//************************************************************************************
	/**
	 * Sprawdza czy istnieje taka relacje
	 * @param string $name
	 */
	public function hasRelation($name) {
		return isset($this->relations[$name]);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return ORMTableRecordRelationInstance
	 */
	public function getRelation($name) {
		if ($this->relations[$name]) {
			return $this->relations[$name];
		} else {
			throw new ORMException(sprintf('Relation %s not found in table %s', $name, $this->getTable()->getTableName()));
		}
	}
	
	//************************************************************************************
	/**
	 * Zwraca pierwsza relacje, ktora jest do tej tabeli
	 * @param ORMTable $oTable
	 * @param string $fieldName
	 * @return ORMTableRecordRelationInstance
	 */
	public function getRelationFor($oTable, $fieldName='') {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		
		foreach($this->relations as $oRelation) {
			if ($oRelation->getForeign()->getTable() === $oTable) {
				$ok = true;
				if ($fieldName) {
					if (!$oRelation->getDefinition()->hasField($fieldName)) $ok = false;
				}
				if ($ok) {
					return $oRelation;
				}
			} 
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param CodeBaseDeclaredClass $oRecordClass
	 * @return ORMTableRecordRelationInstance
	 */
	public function getRelationForRecord($oRecordClass) {
		if (!($oRecordClass instanceof CodeBaseDeclaredClass)) throw new InvalidArgumentException('oRecordClass is not CodeBaseDeclaredClass');
		foreach($this->relations as $oRelation) {
			if ($oRelation->getForeign()->getTable()->getRecordClass() == $oRecordClass) return $oRelation;
		}
		return null;		
	}
	
	//************************************************************************************
	/**
	 * @return ORMField
	 */
	public function getPrimaryKeyField() {
		$oKey = $this->getTable()->getPrimaryKey();
		if ($oKey) {
			return $this->getField($oKey->getFirst());
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * Zwraca domyslna funkcje selektora - czyli f. ktora zwraca NameValuePair[] odpowiadajace nazwa pol i ich wartoscia 
	 * @return Closure
	 */
	public function getDefaultSelectorFunction() {
		$self = $this;
		return function() use($self) {
			$arr = array();
			$oKey = $self->getTable()->getPrimaryKey();
			foreach($oKey->getFields() as $field) {
				$arr[] = new NameValuePair($field, $self->getField($field)->get());
			}
			return $arr;
		};
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getSlug() {
		$arr = array();
		$id = 0;
		if ($oField = $this->getPrimaryKeyField()) {
			$id = $oField->get();
		}
		foreach($this->getFields() as $oField) {
			if ($oField->getDefinition()->getOption('slug')) {
				$arr[] = $oField->get();
			}
		}
		return UtilsString::slugCreate($id, $arr);
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		if ($key == 'pk') {
			$oField = $this->getPrimaryKeyField();
			if ($oField) {
				$oContext->setTag('ORMField', $oField);
				if ($oValuesSource = $oField->getValuesSource()) {
					$oContext->setTag('ValuesSource', $oValuesSource);
					if ($oValuesSource instanceof IEnumerable) {
						$oContext->setTag('IEnumerable', $oValuesSource);
					}
				}
				return $oField->tplRender($oContext);
			} else {
				return '';
			}
		}
		if ($key == 'slug') {
			return $this->getSlug();
		}
		if (isset($this->customFields[$key])) {
			return htmlspecialchars($this->customFields[$key]);
		}
		if ($oField = $this->getField($key)) {
			$oContext->setTag('ORMField', $oField);
			if ($oValuesSource = $oField->getValuesSource()) {
				$oContext->setTag('ValuesSource', $oValuesSource);
				if ($oValuesSource instanceof IEnumerable) {
					$oContext->setTag('IEnumerable', $oValuesSource);
				}
			}
			return $oField->tplRender($oContext);			
		}
		if ($this->hasRelation($key)) {
			$oRelation = $this->getRelation($key);
			if ($oRelation->isMany()) {
				return TemplateRenderableProxy::wrap($oRelation->getRecords());
			} else {
				if ($oRelation->getRecord()) {
					return new TemplateRenderableProxy($oRelation->getRecord());
				} else {
					return array();
				}				
			}
		}
		
		if ($key == 'enumCaption') return htmlspecialchars($this->getEnumCaption());
		
		if (($res = $this->getEvents(self::EVENTS_TPL_RENDER)->callReturn($key, $oContext)) !== null) {
			return $res;
		}
		
		return '';
	}
	
	//************************************************************************************
	public function getEnumCaption() {
		$oField = $this->getPrimaryKeyField();
		if ($oField) {
			return sprintf('[%d] %s', $oField->get(), $this->getEnumCaptionInternal());
		} else {
			return $this->getEnumCaptionInternal();
		}
	}
	
	//************************************************************************************
	protected function getEnumCaptionInternal() {
		if ($oField = $this->getField('name')) {
			return $oField->get();
		} else {
			return '';
		}
	}
	
	//#######################################################################################
	// Funkcje operujace na bazie danych
	//#######################################################################################

	//************************************************************************************
	/**
	 * Wywolywane w momencie doAdd/doUpdate (nawet jesli nic w bazie sie nie zienilo)
	 */
	protected function onSaved() {
		
	}
	
	//************************************************************************************
	/**
	 * Wywolywane w momencie zaladowania rekordu z DB
	 */
	public function onAfterLoad() {
		
	}
	
	//**************************************************************
	/**
	 * @return int  insert ID
	 */
	public function doAdd($force=false) {
		$oPK = $this->getTable()->getPrimaryKey();
		
		$this->getEvents(self::EVENTS_BEFORE_ADD)->call($this);
		foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
			$oExtension->onBeforeAdd($this->getORM(), $this);
		}
		
		$dbRecord = array();
		foreach($this->fields as $oField) {
			if ($oField->getDefinition()->isAutoIncrement() && !$force) continue;
			
			if ($oField->isChanged() || $force) {
				$dbRecord[$oField->getDefinition()->getName()] = $oField;
			}
		}

		if (!empty($dbRecord)) {
			$newID = $this->getSQLStorage()->insertRecord($this->getTable()->getTableName(),$dbRecord);
						
			if ($oPK && $oPK->getCount() == 1 && $newID) {
				$this->getPrimaryKeyField()->set($newID);
				$this->getPrimaryKeyField()->setChanged(false);
			}
			
			$this->onSaved();
			$this->getEvents(self::EVENTS_AFTER_ADD)->call($this);
			foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
				$oExtension->onAfterAdd($this->getORM(), $this);
			}
			
			return $newID;
		} else {
			return false;
		}
	}

	//**************************************************************
	/**
	 * @return bool
	 */
	public function doUpdate($just=null) {
		if (is_string($just)) $just = explode(',', $just);
		
		$this->getEvents(self::EVENTS_BEFORE_UPDATE)->call($this);
		foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
			$oExtension->onBeforeUpdate($this->getORM(), $this);
		}
		
		$dbRecord = array();
		foreach($this->fields as $oField) {
			$ok = true;
			
			if (is_array($just)) {
				if (!in_array($oField->getDefinition()->getName(),$just)) $ok = false;
			}
			
			if (!$ok) continue;
			
			if ($oField->isChanged()) {
				$dbRecord[$oField->getDefinition()->getName()] = $oField;
			}
		}
		
		if ($dbRecord) {
			$this->getSQLStorage()->updateRecord(
				$this->getTable()->getTableName(),
				$dbRecord,
				ORMKey::createEqualCondition($this->getTable()->getTableName(), $this->getTable()->getPrimaryKey(), $this, $this->getTable()->getPrimaryKey())
			);
			
			$this->onSaved();
			$this->getEvents(self::EVENTS_AFTER_UPDATE)->call($this);
			foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
				$oExtension->onAfterUpdate($this->getORM(), $this, true);
			}
			
			return true;
		} else {
			$this->onSaved();
			$this->getEvents(self::EVENTS_AFTER_UPDATE)->call($this);
			foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
				$oExtension->onAfterUpdate($this->getORM(), $this, false);
			}
			return false;
		}
	}

	//**************************************************************
	public function doDelete() {
		$this->getEvents(self::EVENTS_BEFORE_DELETE)->call($this);
		foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
			$oExtension->onBeforeDelete($this->getORM(), $this);
		}
		
		$this->getSQLStorage()->query(sprintf("DELETE FROM `%s` WHERE %s",
			$this->getTable()->getTableName(),
			ORMKey::createEqualCondition($this->getTable()->getTableName(), $this->getTable()->getPrimaryKey(), $this, $this->getTable()->getPrimaryKey())
		));
		
		$this->getEvents(self::EVENTS_AFTER_DELETE)->call($this);
		foreach($this->getORM()->getComponent()->getORMExtensions() as $oExtension) {
			$oExtension->onAfterDelete($this->getORM(), $this);
		}
	}

}

?>