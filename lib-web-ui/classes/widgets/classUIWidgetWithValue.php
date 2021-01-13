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


abstract class UIWidgetWithValue extends UIWidget implements IUIReadable, IUIValidatable {
	
	const CALLBACK_AFTER_READ = 'afterread';
	
	/**
	 * @var ValidationErrorsCollection
	 */
	private $oErrors = null;
	
	/**
	 * @var mixed
	 */
	protected $value = '';
	
	/**
	 * @var IValidator[]
	 */
	protected $validators = array();
	
	//************************************************************************************
	public function getValue() { return $this->value; }
	public function setValue($v) { $this->value = $v; return $this; }
	public function resetValue() { $this->value = ''; }
	public function isValueEmpty() { return $this->getValueString() ? false : true; }
	
	//************************************************************************************
	public function getValueString() { return UtilsString::toString($this->value); }
	public function setValueString($str) { $this->value = strval($str); }
	
	//************************************************************************************
	/**
	 * @param IValidator $oValidator
	 */
	public function addValidator($oValidator) {
		if (!($oValidator instanceof IValidator)) throw new InvalidArgumentException('oValidator is not IValidator');
		$this->validators[] = $oValidator;
	}
	
	//************************************************************************************
	/**
	 * @return ValidationErrorsCollection
	 */
	public function getErrors() {
		return $this->oErrors;
	}
	
	//************************************************************************************
	public function __construct($name, $caption) {
		parent::__construct($name, $caption);
		$this->oErrors = new ValidationErrorsCollection();
		$this->resetValue();
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'Errors': return TemplateRenderableProxy::wrap($this->oErrors->getErrors());
			case 'value': return $this->getValue();
			case 'valueString': return $this->getValueString();
			default: return parent::tplRender($key, $oContext);
		}
	}
	
	//************************************************************************************
	/**
	 * @param ValidationProcess $oProcess
	 */
	public function validationFill($oProcess) {
		if ($this->validators) {
			$oAdder = new ValidationProcessAdder($oProcess);
			$oAdder->setTag('UIWidget', $this);
			foreach($this->validators as $oValidator) {
				$oAdder->addEntry($this->name, $this->value, $oValidator);
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	public function readFromWebRequest($oRequest) {
		$this->onRead($oRequest);
		$this->callbackCall(self::CALLBACK_AFTER_READ, $this, $this->value);
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected abstract function onRead($oRequest);
	
}

?>