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

class HTTPCookiesContainer implements JsonSerializable {
	
	/**
	 * @var HTTPCookie[]
	 */
	private $cookies = array();
	
	
	//************************************************************************************
	/**
	 * @return HTTPCookie[]
	 */
	public function getCookies() { return $this->cookies; }
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isEmpty() { return count($this->cookies) == 0; }
	
	//************************************************************************************
	public function __construct() {
		
	}
	
	
	//************************************************************************************
	/**
	 * @param HTTPCookie $oCookie
	 */
	public function set($oCookie) {
		if (!($oCookie instanceof HTTPCookie)) throw new InvalidArgumentException('oCookie is not HTTPCookie');
		$this->cookies[$oCookie->getName()] = $oCookie;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return HTTPCookie
	 */
	public function get($name) {
		$name = trim($name);
		if ($name) {
			return $this->cookies[$name];
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 */
	public function remove($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		unset($this->cookies[$name]);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function contains($name) {
		$name = trim($name);
		if (!$name) return false;
		return isset($this->cookies[$name]);
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getHeaderString() {
		if (!$this->cookies) return '';
		$arr = array();
		foreach($this->cookies as $oCookie) {
			$arr[] = sprintf('%s=%s', $oCookie->getName(), urlencode($oCookie->getValue()));
		}
		return implode('; ', $arr);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		$arr = array();
		foreach($this->cookies as $oCookie) {
			$arr[] = $oCookie->jsonSerialize();
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return HTTPCookiesContainer
	 */
	public static function jsonUnserialize($arr) {
		$obj = new HTTPCookiesContainer();
		
		if (is_array($arr)) {
			foreach($arr as $v) {
				$oCookie = HTTPCookie::jsonUnserialize($v);
				if ($oCookie) {
					$obj->cookies[] = $oCookie;
				}
			}
		}
		
		return $obj;
	}
	
	
}

?>