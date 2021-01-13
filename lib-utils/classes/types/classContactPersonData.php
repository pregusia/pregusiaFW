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


class ContactPersonData implements JsonSerializable {
	
	private $firstName = "";
	private $lastName = "";
	private $mail = "";
	private $phone = "";
	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFirstName() { return $this->firstName; }
	public function setFirstName($v) { $this->firstName = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getLastName() { return $this->lastName; }
	public function setLastName($v) { $this->lastName = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getMail() { return $this->mail; }
	public function setMail($v) { $this->mail = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getPhone() { return $this->phone; }
	public function setPhone($v) { $this->phone = $v; }
	
	
	
	//************************************************************************************
	public function jsonSerialize() {
		$arr = array(
			"firstName" => $this->firstName,
			"lastName" => $this->lastName,
			"mail" => $this->mail,
			"phone" => $this->phone,
		);
		
		return $arr;
	}
	
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return ContactPersonData
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		$obj = new self();

		$obj->firstName = strval($arr["firstName"]);
		$obj->lastName = strval($arr["lastName"]);
		$obj->mail = strval($arr["mail"]);
		$obj->phone = strval($arr["phone"]);

		return $obj;
	}
	
}

?>