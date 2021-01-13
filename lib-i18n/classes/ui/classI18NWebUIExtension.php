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
 * @NeedLibrary lib-web-ui
 *
 */
class I18NWebUIExtension implements IWebUIExtension {
	
	public function getPriority() { return 1; }
	public function onInit($oComponent) { }
	
	/**
	 * @param IUIRenderable $obj
	 * @param object $ctx
	 * @return string
	 */
	public function adaptTemplateLocation($obj, $ctx=null) {
		return '';
	}
	
	//************************************************************************************
	/**
	 * @param object $obj
	 * @param UIActionsCollection $oActions
	 * @param string $area
	 */
	public function fillObjectActions($obj, $oActions, $area) {
		
	}

	//************************************************************************************
	/**
	 * @param ORMField $oField
	 * @param string $name
	 * @param ComplexString $caption
	 * @return UIWidget
	 */
	public function createWidgetFromORMField($oField, $name, $caption) {
		if ($oField instanceof ORMField_I18NText) {
			return new UIWidget_I18NTextInput($name, $caption);
		}
		if ($oField instanceof ORMField_I18NString) {
			return new UIWidget_I18NStringInput($name, $caption);
		}
		return null;
	}
		
	//************************************************************************************
	/**
	 * @param UIAssetsCollection $oAssets
	 */
	public function prepareAssets($oAssets) {
		$oAssets->addCSSLocation('lib-i18n:css/i18n.css');
		$oAssets->addJSLocation('lib-i18n:js/i18n.js');
	}
	
}

?>