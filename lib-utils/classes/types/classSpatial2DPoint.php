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
 * Punkt w przestrzeni 2D
 * 
 * @author pregusia
 *
 */
class Spatial2DPoint implements JsonSerializable {
	
	private $x = 0;
	private $y = 0;
	
	//************************************************************************************
	public function getX() { return $this->x; }
	public function getY() { return $this->y; }
	
	//************************************************************************************
	public function __construct($x, $y) {
		$this->x = floatval($x);
		$this->y = floatval($y);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'x' => $this->x,
			'y' => $this->y
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return Spatial2DPoint
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		
		if (isset($arr['x']) && isset($arr['y'])) {
			$obj = new Spatial2DPoint(0, 0);
			$obj->x = floatval($arr['x']);
			$obj->y = floatval($arr['y']);
			return $obj;
		}
		
		return null;
	}
	
}

?>