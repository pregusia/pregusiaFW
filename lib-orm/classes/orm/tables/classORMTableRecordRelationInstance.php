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
 * Instancja jakiejs relacji przypieta do rekordu
 * @author pregusia
 *
 */
class ORMTableRecordRelationInstance {

	private $oDefinition = null;
	
	private $oLocalRecord = null;
	
	private $loaded = false;
	
	private $records = array();
	
	//************************************************************************************
	public function getName() {
		return $this->getDefinition()->getName();
	}
	
	//************************************************************************************
	/**
	 * @return ORMRelationDefinition
	 */
	public function getDefinition() { return $this->oDefinition; }
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 */
	public function getLocalRecord() { return $this->oLocalRecord; }
	
	//************************************************************************************
	/**
	 * @return ORMTableAndKeyPair
	 */
	public function getLocal() { return $this->getDefinition()->getLocalFor($this->oLocalRecord); }
	
	//************************************************************************************
	/**
	 * @return ORMTableAndKeyPair
	 */
	public function getForeign() { return $this->getDefinition()->getForeignFor($this->oLocalRecord); }
	
	//************************************************************************************
	public function isLoaded() { return $this->loaded; }
	
	//************************************************************************************
	public function __construct($oDefinition, $oLocalRecord) {
		if (!($oDefinition instanceof ORMRelationDefinition)) throw new InvalidArgumentException('Definition is not ORMRelationDefinition');
		if (!($oLocalRecord instanceof ORMTableRecord)) throw new InvalidArgumentException('LocalRecord is not ORMTableRecord');
		
		$this->oDefinition = $oDefinition;
		$this->oLocalRecord = $oLocalRecord;
		$this->loaded = false;
	}

	//************************************************************************************
	public function isMany() {
		return !$this->getDefinition()->getForeignFor($this->oLocalRecord)->isKeyUnique();
	}
	
	//************************************************************************************
	public function isOne() {
		return $this->getDefinition()->getForeignFor($this->oLocalRecord)->isKeyUnique();
	}	
	
	//************************************************************************************
	/**
	 * Zwraca pierwszy rekord relacji
	 * @return ORMTableRecord
	 */
	public function getRecord($canLoad=true) {
		if (!$this->loaded) {
			if ($canLoad) {
				$this->load();
			} else {
				return null;
			}
		}
		if ($this->records) {
			return UtilsArray::getFirst($this->records);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord[]
	 */
	public function getRecords() {
		if (!$this->loaded) $this->load();
		return $this->records;
	}
	
	//************************************************************************************
	/**
	 * @param ORMTableRecord $oRecord
	 */
	public function internalSetRecord($oRecord) {
		if (!($oRecord instanceof ORMTableRecord)) throw new InvalidArgumentException('Record is not ORMTableRecord');
		if ($this->isOne()) $this->loaded = true;
		$this->records = array();
		$this->records[] = $oRecord;
	}
	
	//************************************************************************************
	public function reset() {
		$this->records = array();
		$this->loaded = false;
	}
	
	//************************************************************************************
	private function load() {
		$oSelect = new ORMQuerySelect();
		$oSelect->addKnownRecord($this->getDefinition(), $this->getLocalRecord());
		$oSelect->addSQLWhere(ORMKey::createEqualCondition('a_main', $this->getForeign()->getKey(), $this->getLocalRecord(), $this->getLocal()->getKey()));
		$this->records = $this->getForeign()->getTable()->getList($oSelect);
		$this->loaded = true;
	}

}

?>