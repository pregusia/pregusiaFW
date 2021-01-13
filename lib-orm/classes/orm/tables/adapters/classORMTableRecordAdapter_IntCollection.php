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


class ORMTableRecordAdapter_IntCollection extends ORMTableRecordAdapter_LinkedTable {

	const FLAG_ALLOW_ZEROS = 1;
	const FLAG_ALLOW_DUPLICATES = 2;
	
	private $valueFieldName = '';
	private $flags = 0;
	
	//************************************************************************************
	public function allowZeros() { return ($this->flags & self::FLAG_ALLOW_ZEROS) != 0; }
	public function allowDuplicates() { return ($this->flags & self::FLAG_ALLOW_DUPLICATES) != 0; }
	
	//************************************************************************************
	/**
	 * @param ORMTableRecord $oRecord
	 * @param string $foreignTableName Nazwa tabeli z wartosciami
	 * @param string $foreignTableValueFieldName Nazwa pola wartosci w tabeli z wartosciami
	 * @param Closure $selectorFunc Funckja zwracajaca NameValuePair[] w roli selektora na obcej tabeli 
	 */
	public function __construct($oRecord, $linkedTableName, $valueFieldName, $flags, $oSelectorFunc) {
		
		$valueFieldName = trim($valueFieldName);
		if (!$valueFieldName) throw new InvalidArgumentException('valueFieldName is empty');
		 
		$this->valueFieldName = $valueFieldName;
		$this->flags = intval($flags);
		
		parent::__construct($oRecord, $linkedTableName, $oSelectorFunc);
	}
	
	//************************************************************************************
	/**
	 * @param SQLResultsRow $oRow
	 * @return mixed
	 */
	protected function internalParseValue($oRow) {
		$v = intval($oRow->getColumn($this->valueFieldName)->getValueRaw());
		if ($this->allowZeros() || $v) {
			return $v;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @return array[] Tablica wartosci do zapisania, kazda pozycja to tablica par k=>v
	 */
	protected function internalGetSQLValues() {
		$res = array();
		foreach($this->internalGetValues() as $val) {
			$row = array();
			$row[$this->valueFieldName] = intval($val);
			$res[] = $row;
		}
		return $res;
	}
	
	//************************************************************************************
	public function getAll() {
		return $this->internalGetValues();
	}
	
	//************************************************************************************
	public function addAll($arr) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not Array');
		
		$num = 0;
		foreach($arr as $v) {
			if ($this->add($v)) {
				$num += 1;
			}
		}
		return $num;
	}
	
	//************************************************************************************
	public function setAll($arr) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not Array');
		$this->clear();
		foreach($arr as $v) {
			$this->add($v);
		}
	}
	
	//************************************************************************************
	public function has($v) {
		return in_array($v, $this->getAll());
	}
	
	//************************************************************************************
	public function contains($v) {
		return $this->has($v);
	}
	
	//************************************************************************************
	public function add($v) {
		$v = intval($v);
		
		if ($this->has($v) && !$this->allowDuplicates()) return false;
		if (!$v && !$this->allowZeros()) return false;
		
		$this->internalAddValue($v);
		return true;
	}
	
	//************************************************************************************
	public function remove($v) {
		$v = intval($v);
		
		$arr = array();
		foreach($this->getAll() as $vv) {
			if ($vv != $v) $arr[] = $vv;
		}
		
		$this->internalSetValues($arr);
	}
	
	//************************************************************************************
	public function setFromStr($str, $sep=',') {
		$this->clear();
		foreach(explode($sep, $str) as $s) {
			$this->add(trim($s));
		}
	}
	
	//************************************************************************************
	public function toString() {
		return implode(', ', $this->getAll());
	}
	
	//************************************************************************************
	/**
	 * Zwraca rekordy obce jako relacja many-to-many
	 * @param ORMTable $oTable
	 * @return ORMTableRecord[]
	 */
	public function getAsRecords($oTable) {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		if (!$oTable->getPrimaryKey()) throw new InvalidArgumentException('Given table has no primary key');
		
		$oSelect = new ORMQuerySelect();
		$oSelect->addCondition($oTable->getPrimaryKey()->getFirst(), ORMQueryConditionFactory::makeIn($this->getAll()));
		
		return $oTable->getList($oSelect);
	}
	
}

?>