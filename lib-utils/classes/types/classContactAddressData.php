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
 * Klasa danych kontaktowych
 * @author pregusia
 *
 */
class ContactAddressData implements JsonSerializable {
	
	private $name = "";
	private $streetName = "";
	private $streetHN = "";
	private $streetAN = "";
	private $cityName = "";
	private $cityPostalCode = "";
	private $country = "";
	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() { return $this->name; }
	public function setName($v) { $this->name = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getStreetName() { return $this->streetName; }
	public function setStreetName($v) { $this->streetName = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getStreetHN() { return $this->streetHN; }
	public function setStreetHN($v) { $this->streetHN = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getStreetAN() { return $this->streetAN; }
	public function setStreetAN($v) { $this->streetAN = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getCityName() { return $this->cityName; }
	public function setCityName($v) { $this->cityName = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getCityPostalCode() { return $this->cityPostalCode; }
	public function setCityPostalCode($v) { $this->cityPostalCode = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getCountry() { return $this->country; }
	public function setCountry($v) { $this->country = $v; }
	
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case "name": return $this->name;
			case "streetName": return $this->streetName;
			case "streetHN": return $this->streetHN;
			case "streetAN": return $this->streetAN;
			case "cityName": return $this->cityName;
			case "cityPostalCode": return $this->cityPostalCode;
			case "country": return $this->country;
			default: return '';
		}
	}
	
	//************************************************************************************
	public function getStreetFull() {
		if ($this->getStreetAN() && $this->getStreetHN()) {
			return sprintf('%s %s/%s', $this->getStreetName(), $this->getStreetHN(), $this->getStreetAN());
		} else {
			return sprintf('%s %s', $this->getStreetName(), $this->getStreetHN());
		}
	}
	
	//************************************************************************************
	public function hashCode() {
		return md5($this->name . $this->streetName . $this->streetAN . $this->streetHN . $this->cityName . $this->cityPostalCode . $this->country);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		$arr = array(
			"name" => $this->name,
			"streetName" => $this->streetName,
			"streetHN" => $this->streetHN,
			"streetAN" => $this->streetAN,
			"cityName" => $this->cityName,
			"cityPostalCode" => $this->cityPostalCode,
			"country" => $this->country,
		);
		
		
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return ContactAddressData
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["name"]) {
			$obj = new self();
	
			$obj->name = strval($arr["name"]);
			$obj->streetName = strval($arr["streetName"]);
			$obj->streetHN = strval($arr["streetHN"]);
			$obj->streetAN = strval($arr["streetAN"]);
			$obj->cityName = strval($arr["cityName"]);
			$obj->cityPostalCode = strval($arr["cityPostalCode"]);
			$obj->country = strval($arr["country"]);
	
			return $obj;
		}
	}
	
	
	
}

?>