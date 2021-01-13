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
 * Rozmiar w przestrzeni 2D
 * 
 * @author pregusia
 *
 */
class Spatial2DSize implements JsonSerializable {
	
	private $width = 0;
	private $height = 0;
	
	//************************************************************************************
	public function getWidth() { return $this->width; }
	public function getHeight() { return $this->height; }
	
	//************************************************************************************
	public function __construct($width, $height) {
		$this->width = floatval($width);
		$this->height = floatval($height);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'width' => $this->width,
			'height' => $this->height
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return Spatial2DSize
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		
		if (isset($arr['width']) && isset($arr['height'])) {
			$obj = new Spatial2DSize(0, 0);
			$obj->width = floatval($arr['width']);
			$obj->height = floatval($arr['height']);
			return $obj;
		}
		
		return null;
	}
	
}

?>