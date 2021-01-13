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

class ExpressionParserEvaluator_BoolClosure implements IExpressionParserEvaluator {
	
	private $oClosure = null;
	
	//************************************************************************************
	/**
	 * @param Closure $oClosure
	 */
	public function __construct($oClosure) {
		if (!($oClosure instanceof Closure)) throw new InvalidArgumentException('oClosure is not Closure');
		$this->oClosure = $oClosure;
	}

	//************************************************************************************
	/**
	 * @param string $id
	 * @return ExpressionParser_Value
	 */
	public function get($id) {
		if ($id == 'true') return ExpressionParser_Value::CreateBool(true);
		if ($id == 'false') return ExpressionParser_Value::CreateBool(false);
		
		$func = $this->oClosure;
		$res = $func($id);
		
		if ($res) {
			return ExpressionParser_Value::CreateBool(true);
		} else {
			return ExpressionParser_Value::CreateBool(false);
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param ExpressionParser_Value[] $args
	 * @return ExpressionParser_Value
	 */
	public function call($name, $args) {
		return ExpressionParser_Value::CreateBool(false);
	}
	
	//************************************************************************************
	/**
	 * @param string $operator
	 * @param ExpressionParser_Value $oLeft
	 * @param ExpressionParser_Value $oRight
	 * @return ExpressionParser_Value
	 */
	public function handleBinaryOperator($operator, $oLeft, $oRight) {
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param string $operator
	 * @param ExpressionParser_Value $oRight
	 * @return ExpressionParser_Value
	 */
	public function handleUnaryOperator($operator, $oRight) {
		return null;
	}
	
}

?>