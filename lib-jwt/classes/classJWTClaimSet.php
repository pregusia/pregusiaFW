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


class JWTClaimSet implements JsonSerializable {
	
	protected $values = array();
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function has($name) {
		if (!$name) throw new InvalidArgumentException('Empty name');
		return isset($this->values[$name]);
	}
	
	//************************************************************************************
	public function getAll() { return $this->values; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public function get($name) {
		if (!$name) throw new InvalidArgumentException('Empty name');
		return $this->values[$name];
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function set($name, $value) {
		if (!$name) throw new InvalidArgumentException('Empty name');
		if ($value) {
			$this->values[$name] = $value;
		} else {
			unset($this->values[$name]);
		}
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function serialize() {
		return UtilsString::base64URLEncodeJSON($this->values);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return $this->values;
	}
	
}

?>