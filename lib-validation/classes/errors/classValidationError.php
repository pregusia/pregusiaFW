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


class ValidationError implements JsonSerializable {
	
	private $fieldName = '';
	
	/**
	 * @var ComplexString
	 */
	private $fieldCaption = null;
	
	/**
	 * @var ComplexString
	 */
	private $errorText = null;
	
	private $errorCode = 0;
	
	//************************************************************************************
	public function getFieldName() { return $this->fieldName; }
	public function getErrorCode() { return $this->errorCode; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getErrorText() { return $this->errorText; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getFieldCaption() { return $this->fieldCaption; }
	
	//************************************************************************************
	public function setFieldCaption($v) {
		$this->fieldCaption = ComplexString::Adapt($v);
	}
	
	//************************************************************************************
	public function __construct($fieldName, $errorCode, $errorText) {
		$this->fieldName = trim($fieldName);
		$this->errorCode = intval($errorCode);
		$this->errorText = ComplexString::Adapt($errorText);
		$this->fieldCaption = ComplexString::CreateEmpty();
	}
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	private function getFieldCaptionFinal() {
		if (!$this->fieldCaption->isEmpty()) return $this->fieldCaption;
		
		if ($this->fieldName) {
			if (!UtilsString::startsWith($this->fieldName, '@')) {
				return ComplexString::Adapt($this->fieldName);
			}
		}
		
		return ComplexString::CreateEmpty();
	}
	
	//************************************************************************************
	public function tplRender($key,$oContext) {
		switch($key) {
			case 'fieldName': return $this->fieldName;
			case 'errorCode': return $this->errorCode;
			case 'errorText': return $this->errorText;
			case 'fieldCaption': return $this->getFieldCaptionFinal();
			case 'fieldCaptionEmpty': return $this->getFieldCaptionFinal()->isEmpty();
			default: return '';
		}
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'fieldName' => $this->fieldName,
			'errorCode' => $this->errorCode,
			'fieldCaption' => $this->fieldCaption->jsonSerialize(),
			'errorText' => $this->errorText->jsonSerialize()	
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return ValidationError
	 */
	public static function jsonUnserialize($arr) {
		if ($arr['errorCode'] || $arr['errorText']) {
			$oError = new ValidationError($arr['fieldName'], $arr['errorCode'], ComplexString::jsonUnserialize($arr['errorText']));
			$oError->setFieldCaption(ComplexString::jsonUnserialize($arr['fieldCaption']));
			return $oError;
		}
		return null;
	}
	
}

?>