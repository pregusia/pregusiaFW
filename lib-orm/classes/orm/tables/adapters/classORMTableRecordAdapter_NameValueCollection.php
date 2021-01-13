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


class ORMTableRecordAdapter_NameValueCollection extends ORMTableRecordAdapter_LinkedTable {

	const FLAG_ALLOW_DUPLICATED_NAMES = 2;
	
	private $flags = 0;
	
	//************************************************************************************
	public function allowDuplicatedNames() { return ($this->flags & self::FLAG_ALLOW_DUPLICATED_NAMES) != 0; }
	
	//************************************************************************************
	/**
	 * @param ORMTableRecord $oRecord
	 * @param string $foreignTableName Nazwa tabeli z wartosciami
	 * @param string $foreignTableValueFieldName Nazwa pola wartosci w tabeli z wartosciami
	 * @param Closure $selectorFunc Funckja zwracajaca NameValuePair[] w roli selektora na obcej tabeli 
	 */
	public function __construct($oRecord, $linkedTableName, $flags, $oSelectorFunc) {
		$this->flags = intval($flags);
		parent::__construct($oRecord, $linkedTableName, $oSelectorFunc);
	}
	
	//************************************************************************************
	/**
	 * @param SQLResultsRow $oRow
	 * @return mixed
	 */
	protected function internalParseValue($oRow) {
		$name = $oRow->getColumn('name')->getValueMapped();
		$value = $oRow->getColumn('value')->getValueMapped();
		if ($name && $value) {
			return new NameValuePair($name, $value);
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @return array[] Tablica wartosci do zapisania, kazda pozycja to tablica par k=>v
	 */
	protected function internalGetSQLValues() {
		$res = array();
		foreach($this->internalGetValues() as $oPair) {
			false && $oPair = new NameValuePair();
			$row = array();
			$row['name'] = $oPair->getName();
			$row['value'] = $oPair->getValue();
			$res[] = $row;
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @return NameValuePair[]
	 */
	public function getPairs() {
		return $this->internalGetValues();
	}
	
	//************************************************************************************
	public function getAssoc() {
		$arr = array();
		foreach($this->getPairs() as $oPair) {
			$arr[$oPair->getName()] = $oPair->getValue();
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @return PropertiesMap
	 */
	public function getPropertiesMap() {
		return PropertiesMap::CreateFromNameValuePairs($this->getPairs());
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public function get($name) {
		foreach($this->getPairs() as $oPair) {
			if ($oPair->getName() == $name) return $oPair->getValue();
		}
		return '';
	}
	
	//************************************************************************************
	public function hasName($name) {
		foreach($this->getPairs() as $oPair) {
			if ($oPair->getName() == $name) return true;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair $oPair
	 * @return boolean
	 */
	public function hasPair($oPair) {
		if (!($oPair instanceof NameValuePair)) throw new InvalidArgumentException('oPair is not NameValuePair');
		foreach($this->getPairs() as $e) {
			if ($oPair->equals($e)) return true;
		}
		return false;
	}
	
	//************************************************************************************
	public function putPair($oPair) {
		if (!($oPair instanceof NameValuePair)) throw new InvalidArgumentException('oPair is not NameValuePair');
		
		if (!$this->allowDuplicatedNames() && $this->hasName($oPair->getName())) return false;
		
		$this->internalAddValue($oPair);
		
		return true;
	}
	
	//************************************************************************************
	public function putNameValue($name, $value) {
		return $this->putPair(new NameValuePair($name, $value));
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair[] $arr
	 * @return int
	 */
	public function putPairs($arr) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not Array');
		foreach($arr as $v) {
			if (!($v instanceof NameValuePair)) throw new InvalidArgumentException('arr item is not NameValuePair');
		}
		
		$num = 0;
		foreach($arr as $oPair) {
			if ($this->putPair($oPair)) {
				$num += 1;
			}
		}
		
		return $num;
	}
	
	//************************************************************************************
	public function putAssoc($arr) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not Array');
		foreach($arr as $k => $v) {
			$this->putNameValue($k, $v);
		}
	}
	
	//************************************************************************************
	/**
	 * @param PropertiesMap $oMap
	 * @throws InvalidArgumentException
	 */
	public function putPropertiesMap($oMap) {
		if (!($oMap instanceof PropertiesMap)) throw new InvalidArgumentException('oMap is not PropertiesMap');
		$this->putPairs($oMap->getNameValuePairs());
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair[] $arr
	 * @throws InvalidArgumentException
	 */
	public function setPairs($arr) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not Array');
		foreach($arr as $v) {
			if (!($v instanceof NameValuePair)) throw new InvalidArgumentException('arr item is not NameValuePair');
		}
		
		$this->clear();
		$this->putPairs($arr);
	}
	
	//************************************************************************************
	public function setAssoc($arr) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not Array');
		$this->clear();
		$this->putAssoc($arr);
	}
	
	//************************************************************************************
	/**
	 * @param PropertiesMap $oMap
	 */
	public function setPropertiesMap($oMap) {
		if (!($oMap instanceof PropertiesMap)) throw new InvalidArgumentException('oMap is not PropertiesMap');
		$this->clear();
		$this->putPropertiesMap($oMap);
	}

	//************************************************************************************
	public function remove($name) {
		$arr = array();
		foreach($this->getPairs() as $oPair) {
			if ($oPair->getName() != $name) $arr[] = $oPair;
		}
		$this->internalSetValues($arr);
	}
	
}

?>