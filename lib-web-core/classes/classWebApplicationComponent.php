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


// genLink include file
require_once dirname(__FILE__) . '/controllers/globalGenLink.php';

// pubLink include file
require_once dirname(__FILE__) . '/public/globalPubLink.php';


class WebApplicationComponent extends ApplicationComponent {
	
	const STAGE = 100;
	
	/**
	 * @var WebActionDefinition
	 */
	private $actions = array();
	
	//************************************************************************************
	public function getName() { return 'web.core'; }
	public function getStages() { return array(self::STAGE); }
	
	//************************************************************************************
	/**
	 * @return WebActionDefinition[]
	 */
	public function getActionsDefinitions() {
		return $this->actions;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return WebActionDefinition
	 */
	public function getActionDefinition($name) {
		if ($this->actions[$name]) {
			return $this->actions[$name];
		} else {
			return null;
		}
		return null; 
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		CodeBase::ensureLibrary('lib-utils', 'lib-web-core');
		CodeBase::ensureLibrary('lib-http', 'lib-web-core');
		
		// priorytet akcji wynika z priorytetu biblioteki
		foreach(CodeBase::getLibraries() as $oLibrary) {
			foreach($oLibrary->getClassesExtending('WebController') as $oClass) {
				if ($oClass->isAbstract()) continue;
			
				foreach(WebActionDefinition::ScanClass($oClass) as $oAction) {
					foreach($oAction->getNames() as $actionName) {
						$this->actions[$actionName] = $oAction;
					}
				}
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if (!$this->getApplicationContext()->isEnvironmentWeb()) return;

		if ($stage == self::STAGE) {
			$oHttpRequest = $this->getService('IHTTPServerRequest');
			false && $oHttpRequest = new IHTTPServerRequest();
			
			$oHttpResponse = $this->getService('IHTTPServerResponse');
			false && $oHttpResponse = new IHTTPServerResponse();
			
			if ($oHttpRequest && $oHttpResponse) {
				$oWebDispatcher = new WebDispatcher($this, $oHttpRequest, $oHttpResponse);
				$this->registerService('WebDispatcher', '', $oWebDispatcher);
				
				$oWebDispatcher->execute();
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @return IWebCoreExtension[]
	 */
	public function getWebExtensions() {
		return $this->getExtensions('IWebCoreExtension');
	}
	
}

?>