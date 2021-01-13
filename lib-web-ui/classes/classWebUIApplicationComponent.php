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


class WebUIApplicationComponent extends ApplicationComponent {
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() { return 'web.ui'; }
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function getStages() {
		return array(30);
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		CodeBase::ensureLibrary('lib-web-core', 'lib-web-ui');
		CodeBase::ensureLibrary('lib-templating', 'lib-web-ui');
		CodeBase::ensureLibrary('lib-validation', 'lib-web-ui');
		CodeBase::ensureLibrary('lib-utils', 'lib-web-ui');
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		
	}
	
	//************************************************************************************
	/**
	 * @return IWebUIExtension[]
	 */
	public function getUIExtensions() {
		return $this->getExtensions('IWebUIExtension');
	}
	
	//************************************************************************************
	/**
	 * @param mixed $obj
	 * @param string $area
	 * @return UIAction[]
	 */
	public function getObjectActions($obj, $area='') {
		if ($obj instanceof TemplateRenderableProxy) {
			$obj = $obj->getArgument();
		}
		
		$oActions = new UIActionsCollection();
		foreach($this->getUIExtensions() as $oExtension) {
			$oExtension->fillObjectActions($obj, $oActions, $area);
		}
		return $oActions->getActions();
	}
	
	//************************************************************************************
	/**
	 * @param TemplateRenderer $oRenderer
	 * @param mixed $obj
	 * @param string $area
	 */
	public function renderObjectActions($oRenderer, $obj, $area='', $limit=4) {
		if ($obj instanceof TemplateRenderableProxy) {
			$obj = $obj->getArgument();
		}
		
		$oActions = new UIActionsCollection();
		$oActionsRenderer = new UIActionsRenderer($oActions, $area, $limit);
		
		foreach($this->getUIExtensions() as $oExtension) {
			$oExtension->fillObjectActions($obj, $oActions, $area);
		}
		
		return UtilsWebUI::render($oActionsRenderer);
	}
	
	//************************************************************************************
	/**
	 * @return UINotificationsHTTPSessionStorage
	 */
	public function getNotificationsStorage() {
		$oHttpRequest = $this->getService('IHTTPServerRequest');
		false && $oHttpRequest = new IHTTPServerRequest();
		
		if ($oHttpRequest) {
			return new UINotificationsHTTPSessionStorage($oHttpRequest->getSession());
		} else {
			return new UINotificationsHTTPSessionStorage(null);
		}
	}
	
}

?>