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

class HTTPCookie implements JsonSerializable {

	private $name = "";
	private $value = "";
	private $expire = 0;
	private $path = "";
	private $domain = "";
	private $secure = false;
	private $httpOnly = false;
	
	
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
	public function getValue() { return $this->value; }
	public function setValue($v) { $this->value = $v; }
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getExpire() { return $this->expire; }
	public function setExpire($v) { $this->expire = intval($v); }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getPath() { return $this->path; }
	public function setPath($v) { $this->path = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getDomain() { return $this->domain; }
	public function setDomain($v) { $this->domain = $v; }
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isSecure() { return $this->secure; }
	public function setSecure($v) { $this->secure = $v ? true : false; }
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isHTTPOnly() { return $this->httpOnly; }
	public function setHTTPOnly($v) { $this->httpOnly = $v ? true : false; }

	//************************************************************************************
	public function __construct($name, $value, $expire=0, $path='', $domain='', $secure=false, $httpOnly=false) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		$this->name = $name;
		$this->value = $value;
		$this->expire = $expire;
		$this->path = $path;
		$this->domain = $domain;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
	}

	//************************************************************************************
	public function jsonSerialize() {
		$arr = array(
			"name" => $this->name,
			"value" => $this->value,
			"expire" => $this->expire,
			"path" => $this->path,
			"domain" => $this->domain,
			"secure" => $this->secure,
			"httpOnly" => $this->httpOnly,
		);
		
		
		return $arr;
	}
	
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return self
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["name"]) {
			$obj = new self();
	
			$obj->name = strval($arr["name"]);
			$obj->value = strval($arr["value"]);
			$obj->expire = intval($arr["expire"]);
			$obj->path = strval($arr["path"]);
			$obj->domain = strval($arr["domain"]);
			$obj->secure = boolval($arr["secure"]);
			$obj->httpOnly = boolval($arr["httpOnly"]);
	
			return $obj;
		}
		return null;
	}
	
	
}

?>