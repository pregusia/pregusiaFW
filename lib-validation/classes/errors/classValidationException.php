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


class ValidationException extends Exception implements JsonSerializable, IteratorAggregate {
	
	/**
	 * @var ValidationErrorsCollection
	 */
	private $oErrors = null;
	
	//************************************************************************************
	/**
	 * @return ValidationErrorsCollection
	 */
	public function getErrors() { return $this->oErrors; }
	
	//************************************************************************************
	/**
	 * @return ValidationError
	 */
	public function getFirstError() {
		return $this->getErrors()->getFirst();
	}
	
	//************************************************************************************
	public function __construct($arg=null) {
		if ($arg instanceof ValidationError) {
			$this->oErrors = new ValidationErrorsCollection();
			$this->oErrors->add($arg);
			parent::__construct('Validation errors');
		}
		elseif ($arg instanceof ValidationErrorsCollection) {
			$this->oErrors = $arg;
			parent::__construct('Validation errors');
		}
		elseif (is_string($arg)) {
			$this->oErrors = new ValidationErrorsCollection();
			$this->oErrors->add(new ValidationError('arg', 0, $arg));
			parent::__construct($arg);
		}
	}
	
	//************************************************************************************
	public function getIterator() {
		return $this->getErrors()->getIterator();
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'errors' => $this->getErrors()->jsonSerialize()
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return ValidationException
	 */
	public static function jsonUnserialize($arr) {
		if (is_array($arr)) {
			$obj = new ValidationException();
			$obj->oErrors = ValidationErrorsCollection::jsonUnserialize($arr['errors']);
			return $obj;
		}
		return null;
	}
		
}

?>