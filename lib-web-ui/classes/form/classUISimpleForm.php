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

class UISimpleForm implements ITemplateRenderableSupplier {
	
	/**
	 * @var UISimpleFormWidget[]
	 */
	private $widgets = array();
	
	/**
	 * @var ValidationErrorsCollection
	 */
	private $oGeneralErrors = null;
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return UISimpleFormWidget
	 */
	public function getWidget($name) {
		return $this->widgets[$name];
	}
	
	//************************************************************************************
	public function __construct() {
		$this->oGeneralErrors = new ValidationErrorsCollection();
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param int $type
	 * @return UISimpleFormWidget
	 */
	public function addWidget($name, $type) {
		$oWidget = new UISimpleFormWidget($name, $type);
		$this->widgets[$name] = $oWidget;
		return $oWidget;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public function getWidgetValue($name) {
		if ($oWidget = $this->getWidget($name)) {
			return $oWidget->getValue();
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $e
	 * @param ValidationError $e
	 * @param Exception $e
	 */
	public function addGeneralError($e) {
		if (is_string($e)) {
			$this->oGeneralErrors->add(new ValidationError('general', 1, $e));
		}
		elseif ($e instanceof ValidationError) {
			$this->oGeneralErrors->add($e);
		}
		elseif ($e instanceof ValidationException) {
			foreach($e->getErrors() as $oError) {
				false && $oError = new ValidationError();
				if ($oWidget = $this->getWidget($oError->getFieldName())) {
					$oWidget->setError($oError);
				} else {
					$this->oGeneralErrors->add($oError);
				}
			}
		}
		elseif ($e instanceof Exception) {
			$this->oGeneralErrors->add(new ValidationError('exception', 1, UtilsExceptions::toString($e)));
		}
		else {
			throw new InvalidArgumentException('Invalid argument given');
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		if ($key == 'hasError') {
			return $this->hasError();
		}
		
		if ($key == 'Errors') {
			if ($this->hasError()) {
				return TemplateRenderableProxy::wrap($this->getErrors());
			} else {
				return array();
			}
		}
		
		if ($oWidget = $this->getWidget($key)) {
			return new TemplateRenderableProxy($oWidget);
		}
		
		return '';
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oWebRequest
	 */
	public function read($oWebRequest) {
		if (!($oWebRequest instanceof WebRequest)) throw new InvalidArgumentException('oWebRequest is not WebRequest');
		
		$this->oGeneralErrors->clear();
		
		foreach($this->widgets as $oWidget) {
			$oWidget->read($oWebRequest);
		}
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function hasError() {
		if ($this->oGeneralErrors->hasAny()) return true;
		
		foreach($this->widgets as $oWidget) {
			if ($oWidget->hasError()) return true;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @return ValidationErrorsCollection
	 */
	public function getErrors() {
		$oErrors = new ValidationErrorsCollection();
		foreach($this->widgets as $oWidget) {
			if ($oWidget->hasError()) {
				$oErrors->add($oWidget->getError());
			}
		}
		foreach($this->oGeneralErrors as $oError) {
			$oErrors->add($oError);
		}
		return $oErrors;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function Validate() {
		foreach($this->widgets as $oWidget) {
			$oWidget->Validate();
		}
		return !$this->hasError();
	}
	
	//************************************************************************************
	public function ValidateAndThrow() {
		if (!$this->Validate()) {
			throw new ValidationException($this->getErrors());
		}
	}
	
}

?>