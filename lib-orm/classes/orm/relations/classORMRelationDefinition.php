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


class ORMRelationDefinition {

	const FLAG_EXPLICIT_USE = 1;
	
	private $name = '';
	private $flags = 0;

	/**
	 * @var ORMTableAndKeyPair
	 */
	private $sideOne = null;
	
	/**
	 * @var ORMTableAndKeyPair
	 */
	private $sideTwo = null;

	//************************************************************************************
	public function getName() { return $this->name; }
	
	//************************************************************************************
	/**
	 * @return ORMTableAndKeyPair
	 */
	public function getSideOne() { return $this->sideOne; }
	
	//************************************************************************************
	/**
	 * @return ORMTableAndKeyPair
	 */
	public function getSideTwo() { return $this->sideTwo; }
	
	//************************************************************************************
	public function isExplicit() {
		return ($this->flags & self::FLAG_EXPLICIT_USE) != 0;
	}
	
	//************************************************************************************
	public function __construct($name, $sideOne, $sideTwo, $flags=0) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		if (!($sideOne instanceof ORMTableAndKeyPair)) throw new InvalidArgumentException('sideOne is not ORMTableAndKeyPair');
		if (!($sideTwo instanceof ORMTableAndKeyPair)) throw new InvalidArgumentException('sideTwo is not ORMTableAndKeyPair');

		$this->name = $name;
		$this->flags = intval($flags);
		$this->sideOne = $sideOne;
		$this->sideTwo = $sideTwo;
	}
	
	//************************************************************************************
	/**
	 * @param string $param
	 * @return ORMTable
	 */
	private function prepareParam($param) {
		$oTable = null;
		if ($param instanceof ORMTableRecord) $oTable = $param->getTable();
		elseif ($param instanceof ORMTable) $oTable = $param;
		else throw new InvalidArgumentException('Param is not ORMTable nor ORMTableRecord');
		return $oTable;
	}
	
	//************************************************************************************
	/**
	 * Sprawdza czy ta tabela/rekord bierze udzial w tej relacji
	 * @param ORMTable|ORMTableRecord $param
	 */
	public function has($param) {
		$oTable = $this->prepareParam($param);
		
		return $this->getSideOne()->getTable() === $oTable || $this->getSideTwo()->getTable() === $oTable;
	}
	
	//************************************************************************************
	/**
	 * Stwierdza czy ta relacja dotyczy tego pola
	 * @param string $fieldName
	 */
	public function hasField($fieldName) {
		return $this->getSideOne()->getKey()->contains($fieldName) || $this->getSideTwo()->getKey()->contains($fieldName);
	}
	
	//************************************************************************************
	/**
	 * Zwraca lokalna pare patrzac z pkt widzenia parametru
	 * @param ORMTable|ORMTableRecord $param
	 * @return ORMTableAndKeyPair
	 */
	public function getLocalFor($param) {
		$oTable = $this->prepareParam($param);
		if (!$this->has($oTable)) throw new InvalidArgumentException('Table ' . $oTable->getTableName() . ' has nothing in common with relation ' . $this->name);
		
		if ($oTable == $this->getSideOne()->getTable()) {
			return $this->getSideOne();
		}
		if ($oTable == $this->getSideTwo()->getTable()) {
			return $this->getSideTwo();
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * Zwraca obca pare patrzac z pkt widzenia parametru
	 * @param ORMTable|ORMTableRecord $param
	 * @return ORMTableAndKeyPair
	 */
	public function getForeignFor($param) {
		$oTable = $this->prepareParam($param);
		if (!$this->has($oTable)) throw new InvalidArgumentException('Table ' . $oTable->getTableName() . ' has nothing in common with relation ' . $this->name);
		
		if ($oTable == $this->getSideOne()->getTable()) {
			return $this->getSideTwo();
		}
		if ($oTable == $this->getSideTwo()->getTable()) {
			return $this->getSideOne();
		}
		return null;
	}	
	
	
	//************************************************************************************
	/**
	 * Tworzy ta relacje dla tego rekordu
	 * @param ORMTableRecord $oRecord
	 * @return ORMTableRecordRelationInstance
	 */
	public function createRelation($oRecord) {
		if (!$this->has($oRecord)) throw new InvalidArgumentException('Record ' . get_class($oRecord) . ' has nothing in common with relation ' . $this->name);

		return new ORMTableRecordRelationInstance($this, $oRecord);
	}
	
	//************************************************************************************
	/**
	 * Sprawdza czy ta relacja jest wiele do wielu z pkt widzanie parametru
	 * @param ORMTableRecord|ORMTable $param
	 * @return boolean
	 */
	public function isMany($param) {
		return !$this->getForeignFor($param)->isKeyUnique();
	}
	
}

?>