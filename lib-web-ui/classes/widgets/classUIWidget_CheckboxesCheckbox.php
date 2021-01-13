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


class UIWidget_CheckboxesCheckbox implements ITemplateRenderableSupplier {
	
	private $name = '';
	
	/**
	 * @var ComplexString
	 */
	private $caption = null;
	
	private $value = 0;
	
	private $checked = false;
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getCaption() { return $this->caption; }
	
	//************************************************************************************
	public function getChecked() { return $this->checked; }
	public function setChecked($v) { $this->checked = $v; }

	//************************************************************************************
	public function __construct($name, $caption, $value) {
		$this->name = $name;
		$this->caption = ComplexString::AdaptTrim($caption);
		$this->value = intval($value);
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'name': return $this->name;
			case 'value': return $this->value;
			case 'caption': return $this->caption;
			case 'checked': return $this->checked;
			default: return '';
		}
	}
	
	
}

?>