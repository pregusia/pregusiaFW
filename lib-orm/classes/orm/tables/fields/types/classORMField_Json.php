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


class ORMField_Json extends ORMField {

	private $value = null;
	
	//************************************************************************************
	/**
	 * @param array $v
	 */
	public function set($v) {
		if ($v === null) {
			if ($this->getDefinition()->isNullable()) {
				if ($this->value !== null) {
					$this->value = null;
					$this->changed = true;
				}
				return true;
			}
		}
		
		if (is_object($v)) {
			throw new InvalidArgumentException('ORMField_Json value cannot be object');
		}
		
		$this->value = $v;
		$this->changed = true;
		return true;
	}
	
	//************************************************************************************
	public function toSQL($oEscaper) {
		if ($this->value === null) {
			if ($this->getDefinition()->isNullable()) return 'NULL';
			return '"null"';
		}
		
		$str = json_encode($this->value);
		return '"' . $oEscaper->escapeString($str) . '"';
	}
	
	//************************************************************************************
	public function load($val) {
		if ($val === null) {
			$this->value = null;
			$this->setChanged(false);
		}
		elseif (is_string($val)) {
			$this->value = @json_decode($val, true);
			$this->setChanged(false);
		}
	}	
	
	//************************************************************************************
	public function isNull() {
		return $this->value === null;
	}	
	
	
	//************************************************************************************
	public function clear() {
		$this->set(array());
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return array
	 */
	public function get() {
		return $this->value;
	}	
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getOne($key) {
		if (is_array($this->value)) {
			return $this->value[$key];
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @return array
	 */
	public function getAssoc() {
		if (is_array($this->value)) {
			return $this->value;
		} else {
			return array();
		} 
	}
	
	//************************************************************************************
	/**
	 * @return PropertiesMap
	 */
	public function getPropertiesMap() {
		return PropertiesMap::CreateFromAssoc($this->getAssoc());
	}
	
	//************************************************************************************
	/**
	 * @return NameValuePair[]
	 */
	public function getPairs() {
		return $this->getPropertiesMap()->getNameValuePairs();
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @return bool
	 */
	public function hasName($key) {
		if (!$key) return false;
		if (is_array($this->value)) {
			return isset($this->value[$key]);
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
	/**
	 * @param NameValuePair $oPair
	 * @return bool
	 */
	public function putPair($oPair) {
		if (!($oPair instanceof NameValuePair)) throw new InvalidArgumentException('oPair is not NameValuePair');
		
		if (!is_array($this->value)) $this->value = array();
		
		$this->value[$oPair->getName()] = $oPair->getValue();
		$this->setChanged(true);
		
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $value
	 * @return bool
	 */
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
	/**
	 * @param string $key
	 */
	public function remove($key) {
		if (!is_array($this->value)) return;
		unset($this->value[$key]);
		$this->setChanged(true);
	}
		
}

?>