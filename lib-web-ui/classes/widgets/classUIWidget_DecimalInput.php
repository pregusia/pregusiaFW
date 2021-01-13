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


class UIWidget_DecimalInput extends UIWidgetWithValue {
	
	private $length = 0;
	private $precision = 0;
	
	//************************************************************************************
	public function getLength() { return $this->length; }
	public function getPrecision() { return $this->precision; }
	
	//************************************************************************************
	/**
	 * @return Decimal
	 */
	public function getValue() {
		return $this->value;
	}
	
	//************************************************************************************
	public function getValueString() {
		return $this->getValue()->toString($this->precision);
	}
	
	//************************************************************************************
	public function setValue($v) {
		if (!($v instanceof Decimal)) $v = new Decimal($v);
		$this->value = $v;
	}
	
	//************************************************************************************
	public function setValueString($v) {
		$this->setValue($v);
	}
	
	//************************************************************************************
	public function __construct($name, $caption, $length, $precision) {
		parent::__construct($name, $caption);
		$this->length = intval($length);
		$this->precision = intval($precision);
		$this->value = new Decimal(0);
		$this->setPrefix('[ui.icon.calculator/]');
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'precision': return $this->precision;
			case 'length': return $this->length; 
			default: return parent::tplRender($key, $oContext);
		}
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		try {
			$this->value = $oRequest->getDecimal($this->getName());
		} catch(Exception $e) {
			$this->value = new Decimal(0);
		}
	}
	
}

?>