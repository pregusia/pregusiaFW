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


class CodeBaseAnnotation {
	
	private $name = '';
	private $params = array();
	
	//************************************************************************************
	public function __construct($name, $params) {
		if (!$name) throw new InvalidArgumentException('name is empty');
		$this->name = $name;
		$this->params = $params;
	}

	//************************************************************************************
	public function getName() { return $this->name; }
	public function getParam($nr=0) { return $this->params[$nr]; }
	public function getParams() { return $this->params; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFullParam() { return implode(' ',$this->params); }
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @return CodeBaseAnnotation
	 */
	public static function ParseSingle($str) {
		$arr = str_getcsv($str, ' ');
		if ($arr[0]) {
			$name = array_shift($arr);
			return new CodeBaseAnnotation($name, $arr);
		} else {
			return null;
		}
	}
	
}

?>