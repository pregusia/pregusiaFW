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


class UtilsWebUI {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * @param IUIRenderable $oRenderable
	 * @param object $ctx
	 * @return string
	 */
	public static function getTemplateLocation($oRenderable, $ctx=null) {
		if (!($oRenderable instanceof IUIRenderable)) throw new InvalidArgumentException('oRenderable is not IUIRenderable');
		
		$oComponent = ApplicationContext::getCurrent()->getComponent('web.ui');
		false && $oComponent = new WebUIApplicationComponent();

		foreach($oComponent->getUIExtensions() as $oExtension) {
			$res = $oExtension->adaptTemplateLocation($oRenderable, $ctx);
			if ($res) return $res;
		}
		
		$tpl = $oRenderable->uiRenderGetTemplateLocation($ctx);
		if ($tpl) {
			return $tpl;
		} else {
			return sprintf('lib-web-ui:%s', str_replace('_', '.', get_class($oRenderable)));
		}
	}
	
	//************************************************************************************
	/**
	 * @param IUIRenderable $oRenderable
	 * @param object $ctx
	 * @return string
	 */
	public static function render($oRenderable, $ctx=null, $additionalTplVars=array()) {
		if ($oRenderable) {
			if (!($oRenderable instanceof IUIRenderable)) throw new InvalidArgumentException('oRenderable is not IUIRenderable');
			
			$tplLoc = self::getTemplateLocation($oRenderable, $ctx);
			if (!$tplLoc) {
				throw new IllegalStateException(sprintf('Could not find template for %s', get_class($oRenderable)));
			}
			
			$oEngine = ApplicationContext::getCurrent()->getComponent('templating');
			false && $oEngine = new TemplatingEngineApplicationComponent();

			$varName = $oRenderable->uiRenderGetVariableName();
			$vars = array();
			
			if (is_array($additionalTplVars)) {
				foreach($additionalTplVars as $k => $v) {
					$vars[$k] = $v;
				}
			}
			
			if (is_array($varName)) {
				if (count($varName) == 0) throw new IllegalStateException('uiRenderGetVariableName returned empty array');
				if (count($varName) >= 1) {
					// znaczy ze wiele zmiennych
					foreach($varName as $n) {
						if ($n) {
							$vars[$n] = $oRenderable->tplRender($n, $ctx);
						}
					}
				}
			}
			elseif (is_string($varName)) {
				$vars[$varName] = new TemplateRenderableProxy($oRenderable);
			}
			else {
				throw new IllegalStateException('uiRenderGetVariableName returned invalid type');
			}
			
			if ($ctx instanceof TemplateRenderableProxyContext) {
				$vars['RenderContext'] = array(
					'Tags' => $ctx->getTags()	
				);
			}
			if ($ctx instanceof ITemplateRenderableSupplier) {
				$vars['RenderContext'] = new TemplateRenderableProxy($ctx);
			}
			
			return $oEngine->renderTemplateFromLocation($tplLoc, $vars);
		}
		return '';
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param array $params
	 * @return string
	 */
	public static function createDynamicContentRef($name, $params=array()) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('name is empty');
		
		$arr = array();
		$arr['name'] = $name;
		foreach($params as $k => $v) $arr[$k] = $v; 
			
		return UtilsString::urlSafeEncrypt($arr, WebParameterType_encarr::KEY);
	}
		
}

?>