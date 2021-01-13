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


class UIWidget_PropertiesMap extends UIWidgetWithValue {
	
	//************************************************************************************
	/**
	 * @return PropertiesMap
	 */
	public function getValue() {
		if (!($this->value instanceof PropertiesMap)) {
			$this->value = new PropertiesMap();
		}
		return $this->value;
	}
	
	//************************************************************************************
	public function setValue($oMap) {
		if (!($oMap instanceof PropertiesMap)) throw new InvalidArgumentException('oMap is not PropertiesMap');
		$this->value = $oMap;
		return $this;
	}
	
	//************************************************************************************
	public function __construct($name, $caption) {
		parent::__construct($name, $caption);
		$this->value = new PropertiesMap();
	}
	
	//************************************************************************************
	protected function onRead($oRequest) {
		$this->getValue()->clear();
		
		for($i=1;$i<=200;++$i) {
			$key = trim($oRequest->getString($this->getCombinedName($i,'name')));
			$value = trim($oRequest->getString($this->getCombinedName($i,'value')));
			if ($key) {
				$this->getValue()->putMulti($key, $value);
			}
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'Items': return TemplateRenderableProxy::wrap($this->getValue()->getNameValuePairs());
			default: return parent::tplRender($key, $oContext);
		}
	}
	
}

?>