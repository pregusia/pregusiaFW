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


class AuthApplicationComponent extends ApplicationComponent {
	
	const STAGE = 55;
	
	/**
	 * @var IAuthService
	 */
	private $oAuthService = null;
	
	/**
	 * @var HTTPSession
	 */
	private $oHttpSession = null;

	//************************************************************************************
	/**
	 * @return IAuthService
	 */
	public function getAuthService() { return $this->oAuthService; }
	
	//************************************************************************************
	/**
	 * @return HTTPSession
	 */
	public function getHTTPSession() { return $this->oHttpSession; }
	
	//************************************************************************************
	public function getName() { return 'auth'; }
	public function getStages() { return array(self::STAGE); }
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		CodeBase::ensureLibrary('lib-http', 'lib-auth');
		CodeBase::ensureLibrary('lib-utils', 'lib-auth');
	}
	
	//************************************************************************************
	/**
	 * @return IAuthACLExtension[]
	 */
	public function getACLExtensions() {
		return $this->getExtensions('IAuthACLExtension');
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if ($stage == self::STAGE) {

			foreach(CodeBase::getInterface('IAuthService')->getAllInstances() as $oService) {
				$this->oAuthService = $oService;
				break;
			}
			
			
			$oHttpRequest = $this->getService('IHTTPServerRequest');
			if ($oHttpRequest && $this->oAuthService) {
				false && $oHttpRequest = new IHTTPServerRequest();
				$this->oHttpSession = $oHttpRequest->getSession();
				
				$this->oAuthService->onInit($this);
				$this->registerService('IAuthService', '', $this->oAuthService);
			}
		}
	}
	
}

?>