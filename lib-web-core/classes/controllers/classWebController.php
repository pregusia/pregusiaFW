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


abstract class WebController {
	
	private $oDispatcher = null;
	private $oWebRequest = null;
	
	//************************************************************************************
	/**
	 * @return WebDispatcher
	 */
	public function getDispatcher() { return $this->oDispatcher; }
	
	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() { return $this->getDispatcher()->getApplicationContext(); }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return SQLStorage
	 */
	public function getSQLStorage($name) {
		return $this->getApplicationContext()->getService('SQLStorage', $name);
	}
	
	//************************************************************************************
	/**
	 * @return WebUIApplicationComponent
	 */
	public function getUIComponent() {
		return $this->getApplicationContext()->getComponent('web.ui');
	}
	
	//************************************************************************************
	/**
	 * @return WebApplicationComponent
	 */
	public function getWebComponent() { return $this->getDispatcher()->getComponent(); }
	
	//************************************************************************************
	/**
	 * @return IHTTPServerRequest
	 */
	public function getHTTPRequest() { return $this->getApplicationContext()->getService('IHTTPServerRequest'); }
	
	//************************************************************************************
	/**
	 * @return IHTTPServerResponse
	 */
	public function getHTTPResponse() { return $this->getApplicationContext()->getService('IHTTPServerResponse'); }
	
	//************************************************************************************
	/**
	 * @return WebRequest
	 */
	public function getWebRequest() { return $this->oWebRequest; }
	
	//************************************************************************************
	public function __construct($oDispatcher) {
		if (!($oDispatcher instanceof WebDispatcher)) throw new InvalidArgumentException('oDispatcher is not WebDispatcher');
		$this->oDispatcher = $oDispatcher;
		$this->oWebRequest = new WebRequest($this->getHTTPRequest());

		$oDispatcher->getApplicationContext()->registerService('WebRequest', '', $this->oWebRequest);
	}
	
	//************************************************************************************
	public function getCurrentURL() {
		return $this->getHTTPRequest()->getRequestURL();
	}

	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param mixed[] $params
	 */
	public function onBeforeAction($oActionDef, $params) {
		
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param WebResponseBase $oResponse
	 * @return WebResponseBase
	 */
	public function onAfterAction($oActionDef, $oResponse) {
		return $oResponse;
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param Exception $oException
	 * @return WebResponseBase
	 */
	public function onException($oActionDef, $oException) {
		return null;
	}
	
}

?>