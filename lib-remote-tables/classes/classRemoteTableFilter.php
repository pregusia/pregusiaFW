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

class RemoteTableFilter implements JsonSerializable {
	
	private $fields = array();
	
	//************************************************************************************
	public function getFields() { return $this->fields; }
	
	//************************************************************************************
	/**
	 * @param array $value
	 */
	private static function checkArray($value) {
		if (is_array($value)) {
			foreach($value as $e) {
				if (is_object($e)) throw new InvalidArgumentException('array element cannot be object');
				if (is_resource($e)) throw new InvalidArgumentException('array element cannot be resource');
				if (is_array($e)) {
					self::checkArray($e);
				}
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setField($name, $value) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		if (is_object($value)) throw new InvalidArgumentException('value cannot be object');
		if (is_resource($value)) throw new InvalidArgumentException('value cannot be resource');
		if (is_array($value)) {
			self::checkArray($value);
		}

		if (is_string($value)) {
			$value = trim($value);
		}
		
		if ($value) {
			$this->fields[$name] = $value;
		} else {
			unset($this->fields[$name]);
		}
	}

	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasField($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		return isset($this->fields[$name]);
	}	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public function getFieldString($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		$val = $this->fields[$name];
		if (!$val) return '';
		if (is_array($val)) throw new IllegalStateException(sprintf('Value %s is array', $name));
		
		return strval($val);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return int
	 */
	public function getFieldInt($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		$val = $this->fields[$name];
		if (!$val) return 0;
		if (is_array($val)) throw new IllegalStateException(sprintf('Value %s is array', $name));
		
		return intval($val);
	}	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return float
	 */
	public function getFieldFloat($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		$val = $this->fields[$name];
		if (!$val) return 0;
		if (is_array($val)) throw new IllegalStateException(sprintf('Value %s is array', $name));
		
		return floatval($val);
	}	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function getFieldBool($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		$val = $this->fields[$name];
		if (!$val) return false;
		if (is_array($val)) throw new IllegalStateException(sprintf('Value %s is array', $name));

		return $val ? true : false;
	}	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return array
	 */
	public function getFieldArray($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		$val = $this->fields[$name];
		if ($val && is_array($val)) {
			return $val;
		} else {
			return array();
		}
	}
	
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'fields' => $this->fields	
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return RemoteTableFilter
	 */
	public static function jsonUnserialize($arr) {
		if (is_array($arr) && is_array($arr['fields'])) {
			$obj = new RemoteTableFilter();
			foreach($arr['fields'] as $k => $v) {
				$obj->setField($k, $v);
			}
			return $obj;
		}
		return null;
	}	
	
}

?>