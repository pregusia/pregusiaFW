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

class HTTPException extends IOException {
	
	private $statusCode = 500;
	private $additionalInfo = array();
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getStatusCode() { return $this->statusCode; }

	//************************************************************************************
	/**
	 * @return array
	 */
	public function getAdditionalInfo() { return $this->additionalInfo; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getAdditionalInfoField($name) {
		$name = trim($name);
		if (!$name) return null;
		
		if (isset($this->additionalInfo[$name])) {
			return $this->additionalInfo[$name];
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param int $statusCode
	 * @param string $message
	 * @param array $additionalInfo
	 */
	public function __construct($statusCode, $message, $additionalInfo=array()) {
		parent::__construct($message);
		$this->statusCode = intval($statusCode);
		$this->additionalInfo = $additionalInfo;
	}
	
}


?>