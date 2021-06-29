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
 * Prubuje ustalic aktualny jezyk z requesta HTTP
 * @author pregusia
 *
 */
class HTTPRequestI18NCurrentLanguageResolver implements II18NCurrentLanguageResolver {
	
	/**
	 * @var ApplicationContext
	 */
	private $oContext = null;
	
	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() { return $this->oContext; }
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() {
		return 100;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		$this->oContext = $oComponent->getApplicationContext();
	}
	
	//************************************************************************************ 
	/**
	 * @return string
	 */
	public function resolveCurrentLanguage() {
		$oHttpRequest = $this->getApplicationContext()->getService('IHTTPServerRequest');
		if ($oHttpRequest) {
			false && $oHttpRequest = new IHTTPServerRequest();
			if ($oHttpRequest->getCookies()->contains('lang')) {
				return $oHttpRequest->getCookies()->get('lang')->getValue();
			}
			elseif ($oHttpRequest->getGETParameter('lang')) {
				return $oHttpRequest->getGETParameter('lang');
			}
		}
		
		return null;
	}
	
	
}

?>