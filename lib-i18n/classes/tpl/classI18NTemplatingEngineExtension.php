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
class I18NTemplatingEngineExtension implements ITemplatingEngineExtension {
	
	/**
	 * @var TemplatingEngineApplicationComponent
	 */
	private $oTemplatingEngine = null;
	
	//************************************************************************************
	public function getPriority() { return 1; }
	
	//************************************************************************************
	public function onInit($oComponent) {
		if ($oComponent instanceof TemplatingEngineApplicationComponent) {
			$this->oTemplatingEngine = $oComponent;
		}
	}
	
	//************************************************************************************
	/**
	 * @return I18NApplicationComponent
	 */
	public function getI18N() {
		return $this->oTemplatingEngine->getApplicationContext()->getComponent('i18n');
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
		$self = $this;
		
		$oRenderer->registerFunction('__', function($oRenderer, $singular, $plural='', $count=0) use ($self) {
			return $self->getI18N()->translate($singular, $plural, $count);
		});
		$oRenderer->registerFunction('getLang', function($oRenderer, $wordName) use ($self) {
			$args = func_get_args();
			$params = array();
			if (count($args) > 2) {
				// mamy jakies parametry, wiec paczymy co tam jest
				// kazdy array przypisujemy calosciowo
				// kazdy 'name=' traktujemy jako przypisanie do tej wartosci
				
				for($i=2;$i<count($args);++$i) {
					if (is_array($args[$i])) {
						foreach($args[$i] as $k => $v) {
							$params[$k] = $v;
						}
					}
					if (is_string($args[$i]) && UtilsString::endsWith($args[$i], '=')) {
						$name = rtrim($args[$i], '=');
						$val = strval($args[++$i]);
						$params[$name] = $val;
					}
				}
			}
			
			$res = $self->getI18N()->translate($wordName);
			if ($params) {
				return UtilsString::formatSimple($res, $params);
			} else {
				return $res;
			}
		});
	}
	
	//************************************************************************************
	/**
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function onAdaptRenderableProxyContext($oContext) {
		$oContext->setTag('i18n.lang', $this->getI18N()->getCurrentLanguage());
	}
}

?>