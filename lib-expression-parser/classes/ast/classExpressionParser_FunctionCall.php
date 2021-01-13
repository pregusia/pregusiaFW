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

class ExpressionParser_FunctionCall implements IExpressionParserEvaluable {
	
	private $functionName = '';
	private $arguments = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFunctionName() { return $this->functionName; }
	
	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable[]
	 */
	public function getArguments() { return $this->arguments; }

	//************************************************************************************
	/**
	 * @return IExpressionParserEvaluable
	 */
	public function getArgument($nr) { return $this->arguments[$nr]; }
	
	
	//************************************************************************************
	public function __construct($functionName, $arguments) {
		$functionName = trim($functionName);
		if (!$functionName) throw new InvalidArgumentException('Empty function name');
		UtilsArray::checkArgument($arguments, 'IExpressionParserEvaluable');

		$this->functionName = $functionName;
		$this->arguments = $arguments;
	}
	
	//************************************************************************************
	/**
	 * @param IExpressionParserEvaluator $oEvaluator
	 * @return float
	 */
	public function evaluate($oEvaluator) {
		if (!($oEvaluator instanceof IExpressionParserEvaluator)) throw new InvalidArgumentException('oEvaluator is not IExpressionParserEvaluator');
		
		$args = array();
		foreach($this->getArguments() as $arg) {
			$args[] = $arg->evaluate($oEvaluator);
		}
		
		return $oEvaluator->call($this->functionName, $args);
	}
	
}

?>