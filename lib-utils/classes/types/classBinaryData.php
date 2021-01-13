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

class BinaryData implements JsonSerializable {
	
	private $data = "";
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getData() { return $this->data; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getDataBase64() {
		return base64_encode($this->data);
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getSize() {
		return strlen($this->data);
	}
	
	//************************************************************************************
	public function __construct($data='') {
		$this->data = strval($data);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'type' => 'BinaryData',
			"data" => base64_encode($this->data)
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return BinaryData
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["type"] == 'BinaryData') {
			$data = @base64_decode($arr['data']);
			return new BinaryData($data);
		}
		return null;
	}
	
	
}

?>