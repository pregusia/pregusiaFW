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

class ExpressionParser_UnaryOperator implements IExpressionParserEvaluable {
	
	private $op = '';
	
	/**
	 * @var IExpressionParserEvaluable
	 */
	private $oRight = null;
	
	//************************************************************************************
	public function __construct($op, $oRight) {
		if (!($oRight instanceof IExpressionParserEvaluable)) throw new InvalidArgumentException('oRight is not IExpressionParserEvaluable');
		if (!self::isOperatorValid($op)) throw new InvalidArgumentException(sprintf('Unary operator %s is invalid', $op));
		$this->op = $op;
		$this->oRight = $oRight;
	}
	
	//************************************************************************************
	public static function isOperatorValid($op) {
		switch($op) {
			case '!':
				return true;
				
			return false;
		}
	}
	
	//************************************************************************************
	public function evaluate($oEvaluator) {
		$oRight = $this->oRight->evaluate($oEvaluator);
		$res = $oEvaluator->handleUnaryOperator($this->op, $oRight);
		
		if ($res !== null) {
			if (!($res instanceof ExpressionParser_Value)) throw new IllegalStateException('Returned value is not ExpressionParser_Value');
			return $res;
		}
		
		if ($this->op == '!') {
			if ($oRight->boolValue()) {
				return ExpressionParser_Value::CreateNumber(0);
			} else {
				return ExpressionParser_Value::CreateNumber(1);
			}
		}
		
		return $oRight;
	}
	
}

?>