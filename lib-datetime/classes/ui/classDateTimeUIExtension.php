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
class DateTimeUIExtension implements IWebUIExtension {

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
	 * @param string $caption
	 * @return UIWidget
	 */
	public function createWidgetFromORMField($oField, $name, $caption) {
		if ($oField instanceof ORMField_Date) return new UIWidget_DateInput($name, $caption);
		if ($oField instanceof ORMField_Time) return new UIWidget_TimeInput($name, $caption);
		if ($oField instanceof ORMField_DateAndTime) return new UIWidget_DateAndTimeInput($name, $caption);
		if ($oField instanceof ORMField_DateShift) return new UIWidget_DateShiftInput($name, $caption);
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param UIAssetsCollection $oAssets
	 */
	public function prepareAssets($oAssets) {
		$oAssets->addJSLocation('lib-datetime:js/moment.min.js');
		//$oAssets->addJSLocation('lib-datetime:js/bootstrap-datetimepicker-4.15.35.js');
		$oAssets->addJSLocation('lib-datetime:js/bootstrap-datetimepicker-4.17.42.js');
		$oAssets->addJSLocation('lib-datetime:js/ui.js');
		
		$oAssets->addCSSLocation('lib-datetime:css/bootstrap-datetimepicker.min.css');
	}
	
}

?>