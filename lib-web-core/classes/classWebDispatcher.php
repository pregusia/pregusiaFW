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


class WebDispatcher {

	/**
	 * @var WebApplicationComponent
	 */
	private $oComponent = null;
	
	/**
	 * @var IHTTPServerRequest
	 */
	private $oHTTPRequest = null;
	
	/**
	 * @var IHTTPServerResponse
	 */
	private $oHTTPResponse = null;
	
	/**
	 * @var WebBaseLocations
	 */
	private $oBaseLocations = null;
	
	/**
	 * @var WebActionDefinition
	 */
	private $oFoundAction = null;
	
	/**
	 * Nazwa zmatchowanej akcji
	 * musi istniec w ten sposob, bo akcje moga posiadac wiele nazw
	 * 
	 * @var string
	 */
	private $foundActionName = '';
	
	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() { return $this->getComponent()->getApplicationContext(); }
	
	//************************************************************************************
	/**
	 * @return WebApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	/**
	 * @return IHTTPServerRequest
	 */
	public function getHTTPRequest() { return $this->oHTTPRequest; }
	
	//************************************************************************************
	/**
	 * @return IHTTPServerResponse
	 */
	public function getHTTPResponse() { return $this->oHTTPResponse; }

	//************************************************************************************
	/**
	 * @return WebActionDefinition
	 */
	public function getFoundAction() { return $this->oFoundAction; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFoundActionName() { return $this->foundActionName; }
	
	//************************************************************************************
	/**
	 * @param WebApplicationComponent $oComponent
	 * @param IHTTPServerRequest $oHTTPRequest
	 * @param IHTTPServerResponse $oHTTPResponse
	 * @throws InvalidArgumentException
	 */
	public function __construct($oComponent, $oHTTPRequest, $oHTTPResponse) {
		if (!($oComponent instanceof WebApplicationComponent)) throw new InvalidArgumentException('oComponent is not WebApplicationComponent');
		if (!($oHTTPRequest instanceof IHTTPServerRequest)) throw new InvalidArgumentException('oHTTPRequest is not IHTTPServerRequest');
		if (!($oHTTPResponse instanceof IHTTPServerResponse)) throw new InvalidArgumentException('oHTTPResponse is not IHTTPServerResponse');
		
		$this->oComponent = $oComponent;
		$this->oHTTPRequest = $oHTTPRequest;
		$this->oHTTPResponse = $oHTTPResponse;
	}
	
	//************************************************************************************
	/**
	 * @return WebBaseLocations
	 */
	public function getBaseLocations() {
		if (!$this->oBaseLocations) {
			$configValue = $this->getComponent()->getConfig()->getValue('location');
			$this->oBaseLocations = new WebBaseLocations($configValue, $this->getHTTPRequest());
			
			foreach($this->getComponent()->getWebExtensions() as $oExtension) {
				foreach($oExtension->getAdditionalBaseLocations() as $loc) {
					$this->oBaseLocations->addLocation($loc);
				}
			}
			
		}
		return $this->oBaseLocations;
	}	
	
	//************************************************************************************
	private function findAction($pathParts) {
		$this->oFoundAction = null;
		$this->foundActionName = '';

		$rawPath = implode('/', $pathParts);
		
		// first search @WebActionRawPath
		foreach($this->getComponent()->getActionsDefinitions() as $oAction) {
			if ($oAction->matchesRawPath($rawPath)) {
				$this->oFoundAction = $oAction;
				$this->foundActionName = $oAction->getFirstName();
				return;
			}
		}
		
		
		$actions = array();
		
		foreach($this->getComponent()->getActionsDefinitions() as $oAction) {
			foreach($oAction->getNames() as $actionName) {
				$num = substr_count($actionName, '.');
				$actions[] = array($actionName, $num, $oAction);
			}
		}
		
		usort($actions, function($a, $b){
			return $b[1] - $a[1]; 
		});
		
		foreach($actions as $ac) {
			$actionName = $ac[0];
			$num = $ac[1] + 1;
			$oAction = $ac[2];
			
			if (implode('.', array_slice($pathParts, 0, $num)) == $actionName) {
				$this->oFoundAction = $oAction;
				$this->foundActionName = $actionName;
				break;
			}
		}
		
	}
	
	//************************************************************************************
	public function execute() {
		$oWebResponse = null;
		false && $oWebResponse = new WebResponseBase();
				
		try {
			$baseLocation = $this->getBaseLocations()->getLocation();
			
			$url = substr(trim($this->getHTTPRequest()->getRequestURI(),'/'),strlen(ltrim(parse_url($baseLocation, PHP_URL_PATH),'/')));
			$aUrl = parse_url($url);
			if ($aUrl === false) throw new IllegalStateException(sprintf('URL %s is invalid', $url));
			
			$pathParts = array();
			foreach(explode('/', $aUrl['path']) as $p) {
				$pathParts[] = trim($p);
			}
			$params = array();
			parse_str($aUrl['query'], $params);
			
			if (true) {
				$allEmpty = true;
				foreach($pathParts as $p) {
					if ($p) $allEmpty = false;
				}
				if ($allEmpty) {
					$pathParts = array('index');
				}
			}
	
			// teraz wyszukujemy akcji
			$this->findAction($pathParts);
	
			if ($this->getFoundAction()) {
				$this->getApplicationContext()->registerService('WebActionDefinition', '', $this->getFoundAction());
				
				foreach($this->getComponent()->getWebExtensions() as $oExtension) {
					$oExtension->onExecuteInit($this->getFoundAction());
				}
				
				$num = substr_count($this->foundActionName, '.') + 1;
				for($i=0;$i<$num;++$i) array_shift($pathParts);
				
				// teraz zdejmujemy args
				foreach($this->getFoundAction()->getParameterNames() as $paramName) {
					if (!$pathParts) break;
					$params[$paramName] = array_shift($pathParts);
				}
				
				$oWebResponse = $this->getFoundAction()->execute($this, $params);
				
				foreach($this->getComponent()->getWebExtensions() as $oExtension) {
					$oWebResponse = $oExtension->onExecuteReturn($this->getFoundAction(), $oWebResponse);
				}
				
			} else {
				throw new WebActionNotFoundException();
			}
			
		} catch(WebResponseReturnException $e) {
			$oWebResponse = $e->getResponse();
			
		} catch(Exception $e) {
			$oWebResponse = WebResponsePlainText::CreateExceptionReport($e);
			
			foreach($this->getComponent()->getWebExtensions() as $oExtension) {
				try {
					$oWebResponse = $oExtension->onExecuteException($e, $oWebResponse);
				} catch(Exception $e2) {
					Logger::warn('processing exception', $e2);
				}
			}
		}
		
		if (!($oWebResponse instanceof WebResponseBase)) $oWebResponse = new WebResponsePlainText(sprintf('oWebResponse is not WebResponseBase'));
		$oWebResponse->finish($this->getHTTPResponse());
	}
	
}

?>