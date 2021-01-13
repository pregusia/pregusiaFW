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

class ExpressionParser_Parser {
	
	/**
	 * @var ExpressionParser_Tokenizer
	 */
	private $oTokenizer = null;
	
	
	//************************************************************************************
	private function __construct($oTokenizer) {
		if (!($oTokenizer instanceof ExpressionParser_Tokenizer)) throw new InvalidArgumentException('oTokenizer is not ExpressionParser_Tokenizer');
		$this->oTokenizer = $oTokenizer;
	}
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function expression() {
		return $this->logicalOrExpression();
	}
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function logicalOrExpression() {
		$expr = $this->logicalAndExpression();
		if (!$expr) return null;
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_OPERATOR_BINARY, '||')) {
			$this->oTokenizer->markSet();
			$this->oTokenizer->popNextValue();
			
			$expr2 = $this->logicalOrExpression();
			if ($expr2) {
				$this->oTokenizer->markCancel();
				return new ExpressionParser_BinaryOperator('||', $expr, $expr2);
			} else {
				$this->oTokenizer->markBack();
				throw new ExpressionParser_Exception('Excepting expression after ||');
			}
		}
		
		return $expr;
	}
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function logicalAndExpression() {
		$expr = $this->equalityExpression();
		if (!$expr) return null;
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_OPERATOR_BINARY, '&&')) {
			$this->oTokenizer->markSet();
			$this->oTokenizer->popNextValue();
			
			$expr2 = $this->logicalAndExpression();
			if ($expr2) {
				$this->oTokenizer->markCancel();
				return new ExpressionParser_BinaryOperator('&&', $expr, $expr2);
			} else {
				$this->oTokenizer->markBack();
				throw new ExpressionParser_Exception('Excepting expression after &&');
			}
		}
		
		return $expr;
	}
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function equalityExpression() {
		$expr = $this->relationalExpression();
		if (!$expr) return null;
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_OPERATOR_BINARY, array('==', '!=', '~='))) {
			$this->oTokenizer->markSet();
			$operator = $this->oTokenizer->popNextValue();
			
			$expr2 = $this->equalityExpression();
			if ($expr2) {
				$this->oTokenizer->markCancel();
				return new ExpressionParser_BinaryOperator($operator, $expr, $expr2);
			} else {
				$this->oTokenizer->markBack();
				throw new ExpressionParser_Exception(sprintf('Excepting expression after %s', $operator));
			}
		}
		
		return $expr;
	}	
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function relationalExpression() {
		$expr = $this->additiveExpression();
		if (!$expr) return null;
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_OPERATOR_BINARY, array('>', '>=','<','<='))) {
			$this->oTokenizer->markSet();
			$operator = $this->oTokenizer->popNextValue();
			
			$expr2 = $this->relationalExpression();
			if ($expr2) {
				$this->oTokenizer->markCancel();
				return new ExpressionParser_BinaryOperator($operator, $expr, $expr2);
			} else {
				$this->oTokenizer->markBack();
				throw new ExpressionParser_Exception(sprintf('Excepting expression after %s', $operator));
			}
		}
		
		return $expr;
	}
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function additiveExpression() {
		$expr = $this->multiplicativeExpression();
		if (!$expr) return null;
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_OPERATOR_BINARY, array('+', '-'))) {
			$this->oTokenizer->markSet();
			$operator = $this->oTokenizer->popNextValue();
			
			$expr2 = $this->additiveExpression();
			if ($expr2) {
				$this->oTokenizer->markCancel();
				return new ExpressionParser_BinaryOperator($operator, $expr, $expr2);
			} else {
				$this->oTokenizer->markBack();
				throw new ExpressionParser_Exception(sprintf('Excepting expression after %s', $operator));
			}
		}
		
		return $expr;
	}	
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function multiplicativeExpression() {
		$expr = $this->prefixExpression();
		if (!$expr) return null;
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_OPERATOR_BINARY, array('*','/'))) {
			$this->oTokenizer->markSet();
			$operator = $this->oTokenizer->popNextValue();
			
			$expr2 = $this->multiplicativeExpression();
			if ($expr2) {
				$this->oTokenizer->markCancel();
				return new ExpressionParser_BinaryOperator($operator, $expr, $expr2);
			} else {
				$this->oTokenizer->markBack();
				throw new ExpressionParser_Exception(sprintf('Excepting expression after %s', $operator));
			}
		}
		
		return $expr;
	}	
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function prefixExpression() {
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_OPERATOR_UNARY)) {
			$this->oTokenizer->markSet();
			$operator = $this->oTokenizer->popNextValue();
			$expr = $this->prefixExpression();
			if ($expr) {
				$this->oTokenizer->markCancel();
				return new ExpressionParser_UnaryOperator($operator, $expr);
			} else {
				$this->oTokenizer->markBack();
				throw new ExpressionParser_Exception(sprintf('Excepting expression after %s', $operator));
			}
		}
		
		return $this->callExpression();
	}
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function callExpression() {
		$expr = $this->primaryExpression();
		if (!$expr) return null;
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_BRACKET_OPEN)) {
			$this->oTokenizer->markSet();
			$this->oTokenizer->popNextValue();
			
			$args = array();
			$needComma = false;
			while(true) {
				if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_BRACKET_CLOSE)) {
					$this->oTokenizer->popNextValue();
					break;
				}
				
				if ($needComma) {
					if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_COMMA)) {
						$this->oTokenizer->popNextValue();
						$needComma = false;
						continue;
					} else {
						throw new ExpressionParser_Exception('Excepting ,');
					}
				}

				$arg = $this->expression();
				if (!$arg) {
					throw new ExpressionParser_Exception('Excepting expression in function call');
				}
				
				$args[] = $arg;
				$needComma = true;
			}
			
			$this->oTokenizer->markCancel();
			
			if ($expr instanceof ExpressionParser_Identifier) {
				return new ExpressionParser_FunctionCall($expr->getIdentifier(), $args);
			} else {
				throw new ExpressionParser_Exception('Only function can be called');
			}
		}
		
		return $expr;
	}
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function primaryExpression() {
		$this->oTokenizer->markSet();
		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_ID)) {
			$this->oTokenizer->markCancel();
			$val = $this->oTokenizer->popNextValue();
			return new ExpressionParser_Identifier($val);
		}
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_NUMBER)) {
			$this->oTokenizer->markCancel();
			$val = $this->oTokenizer->popNextValue();
			return new ExpressionParser_Primitive(floatval($val));
		}
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_STRING)) {
			$this->oTokenizer->markCancel();
			$val = $this->oTokenizer->popNextValue();
			return new ExpressionParser_Primitive(strval($val));
		}		
		if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_BRACKET_OPEN)) {
			$this->oTokenizer->popNextValue();
			$expr = $this->expression();
			if (!$expr) throw new ExpressionParser_Exception('Excepting expression');
			if ($this->oTokenizer->isNext(ExpressionParser_Tokenizer::TOKEN_BRACKET_CLOSE)) {
				$this->oTokenizer->popNextValue();
				$this->oTokenizer->markCancel();
				return $expr;
			} else {
				throw new ExpressionParser_Exception('Excepting )');
			}
		}
		
		$this->oTokenizer->markBack();
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @return IExpressionParserEvaluable
	 */
	public static function Parse($str) {
		$oTokenizer = ExpressionParser_Tokenizer::Tokenize($str);
		if ($oTokenizer) {
			$oParser = new ExpressionParser_Parser($oTokenizer);
			return $oParser->expression();
		}
		return new ExpressionParser_Primitive(0);
	}
	
}

?>