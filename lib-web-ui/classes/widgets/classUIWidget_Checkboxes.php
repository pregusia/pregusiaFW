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


class UIWidget_Checkboxes extends UIWidgetWithValue {
	
	/**
	 * @var UIWidget_CheckboxesCheckbox[]
	 */
	private $checkboxes = array();
	
	private $nr = 1;
	
	//************************************************************************************
	public function __construct($name, $caption) {
		parent::__construct($name, $caption);
		$this->value = 0;
	}
	
	//************************************************************************************
	/**
	 * @return UIWidget_Checkboxes
	 */
	public function setValue($v) {
		parent::setValue(intval($v));
		foreach($this->checkboxes as $oCheckbox) {
			if ($this->value & $oCheckbox->getValue()) {
				$oCheckbox->setChecked(true);
			} else {
				$oCheckbox->setChecked(false);
			}
		}
		return $this;
	}
	
	//************************************************************************************
	/**
	 * @return UIWidget_CheckboxesCheckbox[]
	 */
	public function getCheckboxes() { return $this->checkboxes; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param ComplexString $caption
	 * @return UIWidget_Checkboxes
	 */
	public function addCheckbox($name, $caption, $checked=false) {
		$name = trim($name);
		
		if (!$name) throw new InvalidArgumentException('Empty name');
		if ($this->checkboxes[$name]) throw new InvalidArgumentException('Checkbox with such name already exists');
		
		$oCheckbox = new UIWidget_CheckboxesCheckbox($name, $caption, $this->nr);
		
		$this->checkboxes[$name] = $oCheckbox;
		$this->nr *= 2;
		
		if ($checked) {
			$this->setChecked($name, true);
		}
		
		return $this;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param bool $checked
	 * @return UIWidget_Checkboxes
	 */
	public function setChecked($name, $checked) {
		if ($oChecbox = $this->checkboxes[$name]) {
			if ($checked) {
				$this->value |= $oChecbox->getValue();
				$oChecbox->setChecked(true);
			} else {
				$this->value &= ~$oChecbox->getValue();
				$oChecbox->setChecked(false);
			}
		}
		return $this;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return boolean
	 */
	public function isChecked($name) {
		if ($oChecbox = $this->checkboxes[$name]) {
			return (intval($this->value) & $oChecbox->getValue()) != 0;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @return string[]
	 */
	public function getAllChecked() {
		$arr = array();
		foreach($this->checkboxes as $oCheckbox) {
			if ((intval($this->value) & $oCheckbox->getValue()) != 0) {
				$arr[] = $oCheckbox->getName();
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isAnyChecked() {
		return count($this->getAllChecked()) > 0;
	}
	
	//************************************************************************************
	protected function onRead($oRequest) {
		$this->value = 0;
		foreach($this->checkboxes as $oCheckbox) {
			if ($oRequest->isCheckboxPressed($this->getCombinedName($oCheckbox->getValue()))) {
				$this->value |= $oCheckbox->getValue();
				$oCheckbox->setChecked(true);
			} else {
				$oCheckbox->setChecked(false);
			}
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'Checkboxes': return TemplateRenderableProxy::wrap($this->checkboxes); 
			default: return parent::tplRender($key, $oContext);
		}
	}
	
}

?>