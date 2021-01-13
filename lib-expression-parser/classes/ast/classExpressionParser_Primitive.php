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

class ExpressionParser_Primitive implements IExpressionParserEvaluable {
	
	private $value = false;
	
	//************************************************************************************
	public function __construct($value) {
		if (is_string($value)) $this->value = $value;
		elseif (is_float($value)) $this->value = $value;
		else {
			throw new InvalidArgumentException('Primitive has invalid type');
		}
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isNumber() {
		return is_float($this->value);
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isString() {
		return is_string($this->value);
	}
	
	//************************************************************************************
	public function evaluate($oEvaluator) {
		if ($this->isString()) return ExpressionParser_Value::CreateString($this->value);
		if ($this->isNumber()) return ExpressionParser_Value::CreateNumber($this->value);
		throw new IllegalStateException('Invalid value type');
	}
	
}

?>