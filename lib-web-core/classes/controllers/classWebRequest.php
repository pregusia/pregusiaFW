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


class WebRequest {
	
	private $oHttpRequest = null;
	
	//************************************************************************************
	/**
	 * @return IHTTPServerRequest
	 */
	public function getHTTPRequest() { return $this->oHttpRequest; }
	
	//************************************************************************************
	public function __construct($oHttpRequest) {
		if (!($oHttpRequest instanceof IHTTPServerRequest)) throw new InvalidArgumentException('oHttpRequest is not IHTTPServerRequest');
		$this->oHttpRequest = $oHttpRequest;
	}
	
	//************************************************************************************
	public function getRequestParameter($name, $type) {
		$oType = WebParameterTypeHelper::getType($type, false);
		if (!$oType) throw new InvalidArgumentException(sprintf('Web type %s has no mapper', $type));

		$val = $this->getHTTPRequest()->getPOSTParameter($name);
		if ($this->getHTTPRequest()->getGETParameter($name)) $val = $this->getHTTPRequest()->getGETParameter($name);
		
		return $oType->unserialize($val);
	}
	
	//************************************************************************************
	public function getInteger($name) { return $this->getRequestParameter($name, 'int'); }
	public function getFloat($name) { return $this->getRequestParameter($name, 'float'); }
	public function getString($name) { return $this->getRequestParameter($name, 'string'); }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return Decimal
	 */
	public function getDecimal($name) { return $this->getRequestParameter($name, 'decimal'); }

	//************************************************************************************
	public function isCheckboxPressed($name) {
		return $this->getHTTPRequest()->hasPOSTParameter($name);
	}
	
	//************************************************************************************
	public function getArray($name) {
		$val = $this->getRequestParameter($name, 'raw');
		if (!is_array($val)) $val = array();
		return $val; 
	}
	
	//************************************************************************************
	public function isButtonPressed($btnName) {
		return $this->getHTTPRequest()->hasPOSTParameter($btnName);
	}
	
	//************************************************************************************
	public function wasButtonPressed($btnName) {
		return $this->getHTTPRequest()->hasPOSTParameter($btnName);
	}
	
	//************************************************************************************
	public function isFormSent($formName='') {
		if ($formName) {
			return $this->getString('submitedForm') == $formName;
		} else {
			return $this->getHTTPRequest()->hasPOSTParameter("DO");
		}
	}

	//************************************************************************************
	/**
	 * @return HTTPRequestFile
	 */
	public function getFile($name) {
		return $this->getHTTPRequest()->getFile($name);
	}
	
}

?>