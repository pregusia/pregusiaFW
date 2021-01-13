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
abstract class SecureControllerBase extends WebController {
	
	//************************************************************************************
	/**
	 * @return WebResponseRedirect
	 */
	protected function onCreateLoginRedirect() {
		return new WebResponseRedirect(genLink('auth.login'));
	}
	
	//************************************************************************************
	/**
	 * @return AuthApplicationComponent
	 */
	public function getAuthComponent() {
		return $this->getApplicationContext()->getComponent('auth');
	}
	
	//************************************************************************************
	/**
	 * @return IAuthService
	 */
	public function getAuthService() {
		if ($this->getAuthComponent()) {
			return $this->getAuthComponent()->getAuthService();
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return IAuthorizedUser
	 */
	protected function getLoggedUser() {
		if ($this->getAuthService()) {
			return $this->getAuthService()->getLoggedUser();
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 */
	public function onBeforeAction($oActionDef, $params) {
		foreach($oActionDef->getAnnotations()->getAll('NeedRight') as $oAnno) {
			$right = $oAnno->getParam();
			$oUser = $this->getLoggedUser();
			
			if (!$oUser) {
				throw new WebResponseReturnException($this->onCreateLoginRedirect());
			}
			
			if (!$oUser->authHasRight($right)) {
				throw new SecurityException(sprintf('Logged user dont have needed right %s', $right));
			}
		}
		parent::onBeforeAction($oActionDef, $params);
	}
	
	//************************************************************************************
	/**
	 * @param object $oObject
	 * @param int $operation
	 * @return boolean
	 */
	protected function hasAccessTo($oObject, $operation='other') {
		foreach($this->getAuthComponent()->getACLExtensions() as $oExtension) {
			if ($oExtension->check($this->getLoggedUser(), $oObject, $operation)) {
				return true;
			}
		}
		
		return false;
	}
	
}

?>