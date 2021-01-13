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


class NameValuePair implements JsonSerializable {

	private $name = '';
	private $value = '';
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	public function __construct($name, $value) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		$this->name = $name;
		
		$this->value = $value;
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair $oPair
	 * @return boolean
	 */
	public function equals($oPair) {
		if ($oPair instanceof NameValuePair) {
			return $oPair->getName() == $this->name && $oPair->getValue() == $this->value;
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	public function tplRender($key,$oContext) {
		if ($key == 'name') return $this->name;
		if ($key == 'value') return $this->value;
		return '';
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'name' => $this->name,
			'value' => $this->value	
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return NameValuePair
	 */
	public static function jsonUnserialize($arr) {
		if (is_array($arr) && $arr['name'] && $arr['value']) {
			return new NameValuePair($arr['name'], $arr['value']);
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return NameValuePair[]
	 */
	public static function jsonUnserializeArray($arr) {
		$res = array();
		foreach($arr as $v) {
			if ($obj == self::jsonUnserialize($v)) {
				$res[] = $obj;
			}
		}
		return $res;
	}
		
}

?>