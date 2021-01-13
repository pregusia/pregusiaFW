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
 * 
 * @author pregusia
 * @NeedLibrary lib-web-core
 *
 */
class AuthWebExtension implements IWebCoreExtension {
	
	private $oComponent = null;
	
	//************************************************************************************
	/**
	 * @return ApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	public function getPriority() { return 1; }
	
	//************************************************************************************
	public function onInit($oComponent) {
		$this->oComponent = $oComponent;
	}
	
	//************************************************************************************
	/**
	 * @param WebResponseTemplated $oResponse
	 * @param WebResponseTwoLayersSiteLayout $oResponse
	 */
	public function onBeforeRenderTemplatedResponse($oResponse) {
		$oAuthService = $this->getComponent()->getService('IAuthService');
		if ($oAuthService) {
			false && $oAuthService = new IAuthService();
			if ($oUser = $oAuthService->getLoggedUser()) {
				$oResponse->assignVar('LoggedUser', new TemplateRenderableProxy($oUser));
			} else {
				$oResponse->assignVar('LoggedUser', array());
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @return string[]
	 */
	public function getAdditionalBaseLocations() {
		return array();
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oAction
	 */
	public function onExecuteInit($oAction) {
		
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oAction
	 * @param WebResponseBase $oResponse
	 * @return WebResponseBase
	 */
	public function onExecuteReturn($oAction, $oResponse) {
		return $oResponse;
	}
	
	//************************************************************************************
	/**
	 * @param Exception $e
	 * @param WebResponseBase $oCurrentResponse
	 * @return WebResponseBase
	 */
	public function onExecuteException($e, $oCurrentResponse) {
		return $oCurrentResponse;
	}
	
}

?>