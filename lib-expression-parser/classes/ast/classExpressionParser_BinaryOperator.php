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


class ExpressionParser_BinaryOperator implements IExpressionParserEvaluable {
	
	private $op = '';
	
	/**
	 * @var IExpressionParserEvaluable
	 */
	private $oLeft = null;
	
	/**
	 * @var IExpressionParserEvaluable
	 */
	private $oRight = null;
	
	//************************************************************************************
	public function __construct($op, $oLeft, $oRight) {
		if (!($oLeft instanceof IExpressionParserEvaluable)) throw new InvalidArgumentException('oLeft is not IExpressionParserEvaluable');
		if (!($oRight instanceof IExpressionParserEvaluable)) throw new InvalidArgumentException('oRight is not IExpressionParserEvaluable');
		if (!self::isOperatorValid($op)) throw new InvalidArgumentException(sprintf('Binary operator %s is invalid', $op));
		$this->op = $op;
		$this->oLeft = $oLeft;
		$this->oRight = $oRight;
	}
	
	//************************************************************************************
	public static function isOperatorValid($op) {
		switch($op) {
			case '&&':
			case '||':
			case '+':
			case '-':
			case '*':
			case '/':
			case '>':
			case '>=':
			case '<':
			case '<=':
			case '==':
			case '~=':
			case '!=':
				return true;
			return false;
		}
	}
	
	//************************************************************************************
	public function evaluate($oEvaluator) {
		$oLeft = $this->oLeft->evaluate($oEvaluator);
		$oRight = $this->oRight->evaluate($oEvaluator);
		$res = $oEvaluator->handleBinaryOperator($this->op, $oLeft, $oRight);
		
		if ($res !== null) {
			if (!($res instanceof ExpressionParser_Value)) throw new IllegalStateException('Returned value is not ExpressionParser_Value');
			return $res;
		}
		
		if ($this->op == '&&') return ExpressionParser_Value::CreateBool($oLeft->boolValue() && $oRight->boolValue());
		if ($this->op == '||') {
			if ($oLeft->boolValue()) return $oLeft;
			if ($oRight->boolValue()) return $oRight;
			return ExpressionParser_Value::CreateBool(false);
		}
		
		if ($this->op == '~=') {
			$left = strval($oLeft->getValue());
			$right = strval($oRight->getValue());
			
			if ($right) {
				$pattern = sprintf('/%s/i', $right);
				$res = @preg_match($pattern, $left);
				if ($res) {
					return ExpressionParser_Value::CreateBool(true);
				} else {
					return ExpressionParser_Value::CreateBool(false);
				}
			} else {
				return ExpressionParser_Value::CreateBool(false);
			}
		}

		
		if ($oLeft->isNumber()) {
			$left = $oLeft->getValue();
			$right = floatval($oLeft->getValue());
			
			switch($this->op) {
				case '+': return ExpressionParser_Value::CreateNumber($left + $right);
				case '-': return ExpressionParser_Value::CreateNumber($left - $right);
				case '*': return ExpressionParser_Value::CreateNumber($left * $right);
				case '/': return ExpressionParser_Value::CreateNumber($left / $right);
				
				case '>': return ExpressionParser_Value::CreateBool($left > $right);
				case '>=': return ExpressionParser_Value::CreateBool($left >= $right);
				case '<': return ExpressionParser_Value::CreateBool($left < $right);
				case '<=': return ExpressionParser_Value::CreateBool($left <= $right);
				case '==': return ExpressionParser_Value::CreateBool($left == $right);
				case '!=': return ExpressionParser_Value::CreateBool($left != $right);
			}			
		}
		
		if ($oLeft->isString()) {
			$left = $oLeft->getValue();
			$right = strval($oLeft->getValue());
			
			if ($this->op == '+') {
				return ExpressionParser_Value::CreateString($left . $right);
			}
			
			switch($this->op) {
				case '+': return ExpressionParser_Value::CreateString($left + $right);
				
				case '>': return ExpressionParser_Value::CreateBool(strcmp($left, $right) > 0);
				case '>=': return ExpressionParser_Value::CreateBool(strcmp($left, $right) >= 0);
				case '<': return ExpressionParser_Value::CreateBool(strcmp($left, $right) < 0);
				case '<=': return ExpressionParser_Value::CreateBool(strcmp($left, $right) <= 0);
				case '==': return ExpressionParser_Value::CreateBool(strcmp($left, $right) == 0);
				case '!=': return ExpressionParser_Value::CreateBool(strcmp($left, $right) != 0);
				
				default: return $oLeft;
			}			
		}
		
		return $oLeft;
	}
	
}

?>