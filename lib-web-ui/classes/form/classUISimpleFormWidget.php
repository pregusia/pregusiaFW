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

class UISimpleFormWidget implements ITemplateRenderableSupplier {
	
	const TYPE_STRING = 1;
	const TYPE_INT = 2;
	const TYPE_CHECKBOX = 3;
	
	
	private $name = '';
	private $type = 1;
	private $value = '';
	
	/**
	 * @var ValidationError
	 */
	private $oError = null;
	
	/**
	 * @var IValidator[]
	 */
	private $validators = array();
	
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function getType() { return $this->type; }
	
	//************************************************************************************
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	/**
	 * @param mixed $v
	 * @return UISimpleFormWidget
	 */
	public function setValue($v) {
		$this->value = $v;
		return $this;
	}
	

	//************************************************************************************
	/**
	 * @return ValidationError
	 */
	public function getError() { return $this->oError; }

	//************************************************************************************
	/**
	 * @param ValidationError $oError
	 * @return UISimpleFormWidget
	 */
	public function setError($oError) {
		if ($oError) {
			if (!($oError instanceof ValidationError)) throw new InvalidArgumentException('oError is not ValidationError');
			$this->oError = $oError;
		} else {
			$this->oError = null;
		}
		return $this;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function hasError() {
		if ($this->oError) {
			return true;
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	public function __construct($name, $type) {
		if (!$name) throw new InvalidArgumentException('Empty name');
		$this->name = $name;
		$this->type = $type;
	}
	
	//************************************************************************************
	/**
	 * @param unknown $oValidator
	 * @throws InvalidArgumentException
	 * @return UISimpleFormWidget
	 */
	public function addValidator($oValidator) {
		if (!($oValidator instanceof IValidator)) throw new InvalidArgumentException('oValidator is not IValidator');
		$this->validators[] = $oValidator;
		return $this;
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		switch($key) {
			case 'name': return $this->name;
			case 'value': return htmlspecialchars($this->value);
			case 'error': return $this->getError() ? $this->getError()->getErrorText() : '';
			
			case 'hasError': return $this->getError() ? true : false;
			case 'hasErrorClass': return $this->getError() ? 'has-error' : '';
			
			default: return '';
		}
	}	
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function Validate() {
		$oErrors = new ValidationErrorsCollection();
		foreach($this->validators as $oValidator) {
			$oEntry = new ValidationProcessEntry($this->name, $this->value, $oValidator);
			$oValidator->validate($this->value, $oEntry, $oErrors);
		}
		
		if ($oErrors->hasAny()) {
			$oError = $oErrors->getFirst();
			$this->oError = new ValidationError($this->name, $oError->getErrorCode(), $oError->getErrorText());
			return false;
		} else {
			$this->oError = null;
			return true;
		}
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oWebRequest
	 */
	public function read($oWebRequest) {
		if (!($oWebRequest instanceof WebRequest)) throw new InvalidArgumentException('oWebRequest is not WebRequest');
		
		$this->value = '';
		$this->oError = null;
		
		switch($this->type) {
			case self::TYPE_STRING:
				$this->value = $oWebRequest->getString($this->name);
				break;
				
			case self::TYPE_INT:
				$this->value = $oWebRequest->getInteger($this->name);
				break;
				
			case self::TYPE_CHECKBOX:
				$this->value = $oWebRequest->isCheckboxPressed($this->name);
				break;
				
			default:
				break;
		}
	}
	
}

?>