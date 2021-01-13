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
 * @NeedLibrary lib-templating
 *
 */
class WebTemplatingExtension implements ITemplatingEngineExtension {
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() {
		return 10;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		
	}
	
	//************************************************************************************
	/**
	 * @param TemplatingEngineApplicationComponent $oComponent
	 */
	public function onAdaptComponent($oComponent) {
		
	}

	//************************************************************************************
	/**
	 * @param TemplateRenderer $oRenderer
	 */
	public function onAdaptRenderer($oRenderer) {
		$oRenderer->registerFunction('genLink', function($oRenderer) {
			$args = func_get_args();
			array_shift($args); // $oRenderer
			return call_user_func_array('genLink', $args);
		});
		
		$oRenderer->registerFunction('getBaseLocation', function($oRenderer) {
			$oDispatcher = ApplicationContext::getCurrent()->getService('WebDispatcher');
			false && $oDispatcher = new WebDispatcher();
			
			if ($oDispatcher) {
				return $oDispatcher->getBaseLocations()->getLocation();
			} else {
				return '';
			}
		});
		
		$oRenderer->registerFunction('pubLink', function($oRenderer, $path) {
			return pubLink($path);
		});
		
		$oRenderer->registerFunction('currentURL', function($oRenderer) {
			$oDispatcher = ApplicationContext::getCurrent()->getService('WebDispatcher');
			false && $oDispatcher = new WebDispatcher();
			
			if ($oDispatcher) {
				return $oDispatcher->getHTTPRequest()->getRequestURL();
			} else {
				return WebBaseLocations::getStaticInstance()->getLocation();
			}
		});		
		
		$oRenderer->registerFunction('encodeID', function($oRenderer,$val){
			return UtilsIdEncoder::encodeId($val);
		});
	}
	
	//************************************************************************************
	/**
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function onAdaptRenderableProxyContext($oContext) {
		
	}
	
	
}

?>