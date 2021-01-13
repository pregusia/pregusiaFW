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


class WebLinkBuilder {
	
	private $actionName = '';
	private $params = array();
	
	private $oComponent = null;
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getActionName() { return $this->actionName; }
	
	//************************************************************************************
	/**
	 * @return WebApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	public function __construct($actionName) {
		$this->actionName = trim($actionName);
		
		$this->oComponent = ApplicationContext::getCurrent()->getComponent('web.core');
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getBaseLocation() {
		$oDispatcher = ApplicationContext::getCurrent()->getService('WebDispatcher');
		false && $oDispatcher = new WebDispatcher();
		
		if ($oDispatcher) {
			return $oDispatcher->getBaseLocations()->getLocation();
		} else {
			return WebBaseLocations::getStaticInstance()->getLocation();
		}
	}
	
	//************************************************************************************
	public function setParam($name, $value) {
		if ($value) {
			$this->params[$name] = $value;
		} else {
			unset($this->params[$name]);
		}
	}
	
	//************************************************************************************
	/**
	 * @param ...
	 * @return string
	 */
	public function create() {
		$params = $this->params;
		$args = func_get_args();
		while($args) {
			$name = array_shift($args);
			$value = array_shift($args);
			$params[$name] = $value;
		}
		
		if (!$this->actionName) {
			return $this->getBaseLocation();
		}
		
		$oActionDef = $this->getComponent()->getActionDefinition($this->actionName);
		if (!$oActionDef) return $this->getBaseLocation();
	
		$url = array();
		foreach(explode('.',$oActionDef->getFirstName()) as $p) $url[] = $p;
		foreach($oActionDef->getParameterNames() as $paramName) {
			$paramValue = $params[$paramName];
			if (is_string($paramValue) && UtilsString::startsWith($paramValue, '$TPL') && UtilsString::endsWith($paramValue, '$')) {
				$url[] = $paramValue;
			} else {
				$url[] = $oActionDef->getParameterType($paramName)->serialize($params[$paramName]);
			}
			unset($params[$paramName]);
		}
		
		$query = http_build_query($params);
		return rtrim($this->getBaseLocation() . implode('/', $url) . ($query ? '?' . $query : ''),'/');		
	}
	
}

?>