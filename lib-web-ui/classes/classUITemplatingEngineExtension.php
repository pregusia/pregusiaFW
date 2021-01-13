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


class UITemplatingEngineExtension implements ITemplatingEngineExtension {
	
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
		$oUIComponent = $oRenderer->getComponent()->getApplicationContext()->getComponent('web.ui');
		false && $oUIComponent = new WebUIApplicationComponent();
		
		$oRenderer->registerFunction('renderObjectUIActions', function($oRenderer, $obj, $area='', $limit=4) use ($oUIComponent) {
			return $oUIComponent->renderObjectActions($oRenderer, $obj, $area, $limit);
		});
		$oRenderer->registerFunction('getObjectUIActions', function($oRenderer, $obj, $area='') use ($oUIComponent) {
			return TemplateRenderableProxy::wrap($oUIComponent->getObjectActions($obj, $area));
		});
		$oRenderer->registerFunction('getUINotifications', function($oRenderer) use ($oUIComponent) {
			$res = TemplateRenderableProxy::wrap($oUIComponent->getNotificationsStorage()->getAll());
			$oUIComponent->getNotificationsStorage()->clear();
			return $res;
		});
		$oRenderer->registerFunction('hasUINotifications', function($oRenderer) use ($oUIComponent) {
			return $oUIComponent->getNotificationsStorage()->getAll() ? true : false;
		});
		$oRenderer->registerFunction('renderUINotifications', function($oRenderer) use ($oUIComponent) {
			$html = '';
			foreach($oUIComponent->getNotificationsStorage()->getAll() as $oNotification) {
				$html .= UtilsWebUI::render($oNotification);
			}
			$oUIComponent->getNotificationsStorage()->clear();
			return $html;
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