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
class UIWidget_I18NStringInput extends UIWidgetWithValue {
	
	//************************************************************************************
	public function setValue($v) {
		$this->value = new I18NString($v);
	}
	
	//************************************************************************************
	/**
	 * @return I18NString
	 */
	public function getValue() {
		return $this->value;
	}
	
	//************************************************************************************
	public function setValueString($str) {
		$this->value = new I18NString($str);
	}
	
	//************************************************************************************
	public function getValueString() {
		return $this->getValue()->__toString();
	}
	
	//************************************************************************************
	public function __construct($name, $caption) {
		parent::__construct($name, $caption);
		$this->value = new I18NString();
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-i18n:UIWidget.I18NStringInput';
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {

			case 'ValueArray': return $this->getValue()->getAssoc();
			case 'Languages': return LanguageEnum::getInstance()->getKeys();
			
			default: return parent::tplRender($key, $oContext);
		}
	}
	
	//************************************************************************************
	protected function onRead($oRequest) {
		$arr = array();
		foreach(LanguageEnum::getInstance()->getKeys() as $lang) {
			$arr[$lang] = $oRequest->getString($this->getCombinedName($lang));
		}
		$this->value = new I18NString($arr);
	}
	
}

?>