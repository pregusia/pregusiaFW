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


class SpatialLatLng implements JsonSerializable {

	private $lat = 0;
	private $lng = 0;
	
	//************************************************************************************
	/**
	 * @return float
	 */
	public function getLat() { return $this->lat; }
	
	//************************************************************************************
	/**
	 * @return float
	 */
	public function getLng() { return $this->lng; }
	
	//************************************************************************************
	public function __construct($lat=0, $lng=0) {
		$this->lat = floatval($lat);
		$this->lng = floatval($lng);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			"lat" => $this->lat,
			"lng" => $this->lng,
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return SpatialLatLng
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["lat"]) {
			$obj = new self();
			$obj->lat = floatval($arr["lat"]);
			$obj->lng = floatval($arr["lng"]);
			return $obj;
		}
	}
	
}

?>