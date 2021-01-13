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


class IdWithValues implements JsonSerializable {

	private $id = '';
	private $values = array();
	
	//************************************************************************************
	public function getID() { return $this->id; }

	//************************************************************************************
	public function get($name) {
		return $this->values[$name];
	}
	
	//************************************************************************************
	public function __construct($id, $values) {
		if (!$id) throw new InvalidArgumentException('Empty id');
		if (!is_array($values)) throw new InvalidArgumentException('values is not array');
		$this->id = $id;
		$this->values = $values;
	}
	
	//************************************************************************************
	public function tplRender($key,$oContext) {
		if ($key == 'id') return htmlspecialchars($this->id);
		if ($this->values[$key]) return $this->values[$key];
		return '';
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'id' => $this->id,
			'values' => $this->values	
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return IdWithValues
	 */
	public static function jsonUnserialize($arr) {
		if ($arr['id']) {
			
			$obj = new IdWithValues();
			$obj->id = $arr['id'];
			foreach($arr['values'] as $k => $v) $obj->values[$k] = $v;
			
			return $obj;
		}
		return null;
	}
		
}

?>