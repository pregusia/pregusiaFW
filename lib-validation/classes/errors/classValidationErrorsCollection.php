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


class ValidationErrorsCollection implements JsonSerializable, IteratorAggregate {
	
	/**
	 * @var ValidationError[]
	 */
	private $errors = array();
	
	//************************************************************************************
	public function add($arg) {
		if ($arg instanceof ValidationError) {
			$this->errors[] = $arg;
		}
		elseif (UtilsArray::isIterable($arg)) {
			UtilsArray::checkArgument($arg, 'ValidationError');
			foreach($arg as $v) {
				$this->errors[] = $v;
			}
		}
		elseif ($arg instanceof Exception) {
			$this->errors[] = new ValidationError('', 1, UtilsExceptions::toString($arg));
		}
		else {
			throw new InvalidArgumentException('Given argument is invalid');
		}
	}
	
	//************************************************************************************
	/**
	 * @return ValidationError[]
	 */
	public function getErrors() {
		return $this->errors;
	}

	//************************************************************************************
	public function getIterator() {
        return new ArrayIterator($this->errors);
    }
    
    //************************************************************************************
    public function clear() {
    	$this->errors = array();
    }
    
    //************************************************************************************
    /**
     * @return ValidationError
     */
    public function getFirst() {
    	return UtilsArray::getFirst($this->errors);
    }
    
    //************************************************************************************
    public function getFieldNames() {
    	return array_keys($this->getFieldNamesAsKeys());
    }
    
    //************************************************************************************
    public function getFieldNamesAsKeys() {
    	$names = array();
    	foreach($this->getErrors() as $oError) {
    		if ($oError->getFieldName()) $names[$oError->getFieldName()] = 1;
    	}
    	return $names;
    }
    
    //************************************************************************************
    public function hasAny() {
    	return count($this->errors) > 0;
    }
    
    //************************************************************************************
    public function throwException() {
    	if ($this->errors) {
    		throw new ValidationException($this);
    	}
    }
    
    //************************************************************************************
    /**
     * @param object $ctx
     * @return string
     */
    public function toString($ctx) {
    	$res = array();
    	foreach($this->getErrors() as $oError) {
    		$res[] = sprintf('[%s: %s]', $oError->getFieldName(), $oError->getErrorText()->render($ctx));
    	}
    	return sprintf('[%s]', implode(',', $res));
    }
    
    //************************************************************************************
    /**
     * @param array $mappings
     * @return ValidationErrorsCollection
     */
    public function remapFieldsNames($mappings) {
    	if (!is_array($mappings)) throw new InvalidArgumentException('mappings is not array');
    	
    	$oErrors = new ValidationErrorsCollection();
    	foreach($this->getErrors() as $oError) {
    		if (isset($mappings[$oError->getFieldName()])) {
    			$newName = $mappings[$oError->getFieldName()];
    			if ($newName) {
    				
    				$oNewError = new ValidationError($newName, $oError->getErrorCode(), $oError->getErrorText());
    				$oNewError->setFieldCaption($oError->getFieldCaption());
    				$oErrors->add($oNewError);
    			}
    			
    		} else {
    			$oErrors->add($oError);
    		}
    	}

    	return $oErrors;
    }
    
    //************************************************************************************
    /**
     * @param array $mappings
     * @return ValidationErrorsCollection
     */
    public function remapFieldsCaptions($mappings) {
    	if (!is_array($mappings)) throw new InvalidArgumentException('mappings is not array');
    	
    	$oErrors = new ValidationErrorsCollection();
    	foreach($this->getErrors() as $oError) {
    		if (isset($mappings[$oError->getFieldName()])) {
    			$caption = $mappings[$oError->getFieldName()];
    			if ($caption) {
    				$oNewError = new ValidationError($oError->getFieldName(), $oError->getErrorCode(), $oError->getErrorText());
    				$oNewError->setFieldCaption($caption);
    				$oErrors->add($oNewError);
    			} else {
    				$oErrors->add($oError);
    			}
    		} else {
    			$oErrors->add($oError);
    		}
    	}

    	return $oErrors;    	
    }
    
    //************************************************************************************
    public function jsonSerialize() {
    	$arr = array();
    	foreach($this->errors as $oError) {
    		$arr[] = $oError->jsonSerialize();
    	}
    	return $arr;
    }
    
    //************************************************************************************
    public static function jsonUnserialize($arr) {
    	$obj = new ValidationErrorsCollection();
    	if (is_array($arr)) {
    		foreach($arr as $e) {
    			if ($oError = ValidationError::jsonUnserialize($e)) {
    				$obj->errors[] = $oError;
    			}
    		}
    	}
    	return $obj;
    }
	
}

?>