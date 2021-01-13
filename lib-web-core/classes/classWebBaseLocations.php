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

class WebBaseLocations {
	
	private $locations = array();
	private $oRequest = null;
	
	//************************************************************************************
	/**
	 * @return IHTTPServerRequest
	 */
	public function getHTTPRequest() { return $this->oRequest; }
	
	//************************************************************************************
	public function __construct($configValue, $oRequest) {
		if ($oRequest) {
			if (!($oRequest instanceof IHTTPServerRequest)) throw new InvalidArgumentException('oRequest is not IHTTPServerRequest');
		}
		
		if (is_array($configValue)) {
			foreach($configValue as $loc) {
				$this->addLocation($loc);
			}
		} else {
			$this->addLocation(strval($configValue));
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $loc
	 * @return bool
	 */
	public function addLocation($loc) {
		if (!filter_var($loc, FILTER_VALIDATE_URL)) return false;
		$this->locations[] = rtrim($loc,'/') . '/';
		return true;
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getLocations() {
		return $this->locations;
	}
	
	//************************************************************************************
	/**
	 * Zwraca lokacje ustalona na podstawie httpHost
	 * lub pierwsza badz wyjatek
	 * @return string
	 */
	public function getLocation($returnFirst=false) {
		if (!$this->locations) throw new IllegalStateException('Empty base locations');
			
		if ($this->oRequest) {
			
			$res = '';
			foreach($this->getLocations() as $loc) {
				$locHost = parse_url($loc, PHP_URL_HOST);
				$locScheme = parse_url($loc, PHP_URL_SCHEME);
				
				if ($locHost == $this->getHTTPRequest()->getHost()) {
					if ($locScheme == 'https' && $this->getHTTPRequest()->isSecure()) {
						$res = $loc;
						break;
					}
					if ($locScheme == 'http' && !$this->getHTTPRequest()->isSecure()) {
						$res = $loc;
						break;
					}
				}
			}
			
			if ($res) {
				return $res;
			} else {
				if ($returnFirst) {
					return UtilsArray::getFirst($this->locations);
				} else {
					throw new WebBaseLocationNotFoundException($this->getHTTPRequest()->getRequestURL());
				}
			}
			
		} else {
			// nie mamy requestu - czyli wywolanie statyczne - zwracamy pierwsza znaleziona
			return UtilsArray::getFirst($this->locations);
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $host
	 * @return bool
	 */
	public function containsHost($host) {
		$host = trim($host);
		if (!$host) return false;
		
		foreach($this->getLocations() as $loc) {
			$locHost = parse_url($loc, PHP_URL_HOST);
			if ($locHost == $host) return true;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	private static $INSTANCE = null;
	
	//************************************************************************************
	/**
	 * @return WebBaseLocations
	 */
	public static function getStaticInstance() {
		if (!self::$INSTANCE) {
			$configValue = ApplicationContext::getCurrent()->getConfig()->getValue('web.core.location');
			if (!$configValue) throw new ConfigEntryInvalidValueException('web.core.location');
			
			self::$INSTANCE = new WebBaseLocations($configValue, null);
		}
		return self::$INSTANCE;
	}
	
	
}

?>