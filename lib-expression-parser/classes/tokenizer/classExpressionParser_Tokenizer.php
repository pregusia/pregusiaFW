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

class ExpressionParser_Tokenizer {
	
	const TOKEN_NONE = 0;
	
	const TOKEN_ID = 1;
	const TOKEN_NUMBER = 2;
	const TOKEN_STRING = 3;
	
	const TOKEN_BRACKET_OPEN = 10;
	const TOKEN_BRACKET_CLOSE = 11;
	const TOKEN_OPERATOR_UNARY = 12;
	const TOKEN_OPERATOR_BINARY = 13;
	const TOKEN_COMMA = 14;
	

	
	private $tokens = array();
	private $position = 0;
	private $marks = array();
	
	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	public function markSet() {
		$this->marks[] = $this->position;
	}
	
	//************************************************************************************
	public function markCancel() {
		if (!$this->marks) throw new IllegalStateException('Marks is empty');
		array_pop($this->marks);
	}
	
	//************************************************************************************
	public function markBack() {
		if (!$this->marks) throw new IllegalStateException('Marks is empty');
		$pos = array_pop($this->marks);
		$this->position = $pos;
	}
	
	//************************************************************************************
	public function isNext($type,$value='') {
		if (!$this->tokens[$this->position]) {
			return $type == self::TOKEN_NONE;
		} else {
			if ($value) {
				if (is_array($value)) {
					return $this->tokens[$this->position]['type'] == $type && in_array($this->tokens[$this->position]['value'], $value);
				} else {
					$value = strval($value);
					return $this->tokens[$this->position]['type'] == $type && $this->tokens[$this->position]['value'] == $value;
				}
			} else {
				return $this->tokens[$this->position]['type'] == $type;
			}
		}
	}
	
	//************************************************************************************
	public function popNextValue() {
		$val = '';
		if ($this->tokens[$this->position]) {
			$val = $this->tokens[$this->position]['value'];
		}
		$this->position += 1;
		return $val;
	}
	
	//************************************************************************************
	private static function charIsIDChar($ch) {
		return ctype_alpha($ch) || $ch == '-' || $ch == '_';
	}
	
	//************************************************************************************
	private static function nextToken($str) {
		$str = trim($str);
		if (strlen($str) == 0) return array(self::TOKEN_NONE,'','');
		
		if (substr($str,0,2) == '==') return array(self::TOKEN_OPERATOR_BINARY, '==', substr($str,2));
		elseif (substr($str,0,2) == '>=') return array(self::TOKEN_OPERATOR_BINARY, '>=', substr($str,2));
		elseif (substr($str,0,2) == '!=') return array(self::TOKEN_OPERATOR_BINARY, '!=', substr($str,2));
		elseif (substr($str,0,2) == '<>') return array(self::TOKEN_OPERATOR_BINARY, '!=', substr($str,2));
		elseif (substr($str,0,2) == '~=') return array(self::TOKEN_OPERATOR_BINARY, '~=', substr($str,2));
		elseif (substr($str,0,2) == '<=') return array(self::TOKEN_OPERATOR_BINARY, '<=', substr($str,2));
		elseif (substr($str,0,2) == '&&') return array(self::TOKEN_OPERATOR_BINARY, '&&', substr($str,2));
		elseif (substr($str,0,2) == '||') return array(self::TOKEN_OPERATOR_BINARY, '||', substr($str,2));
		elseif (substr($str,0,1) == '>') return array(self::TOKEN_OPERATOR_BINARY, '>', substr($str,1));
		elseif (substr($str,0,1) == '<') return array(self::TOKEN_OPERATOR_BINARY, '<', substr($str,1));
		elseif (substr($str,0,1) == '=') return array(self::TOKEN_OPERATOR_BINARY, '==', substr($str,1));
		elseif (substr($str,0,1) == '!') return array(self::TOKEN_OPERATOR_UNARY, '!', substr($str,1));
		elseif (substr($str,0,1) == '+') return array(self::TOKEN_OPERATOR_BINARY, '+', substr($str,1));
		elseif (substr($str,0,1) == '-') return array(self::TOKEN_OPERATOR_BINARY, '-', substr($str,1));
		elseif (substr($str,0,1) == '*') return array(self::TOKEN_OPERATOR_BINARY, '*', substr($str,1));
		elseif (substr($str,0,1) == '/') return array(self::TOKEN_OPERATOR_BINARY, '/', substr($str,1));
		elseif (substr($str,0,1) == '(') return array(self::TOKEN_BRACKET_OPEN, '(', substr($str,1));
		elseif (substr($str,0,1) == ')') return array(self::TOKEN_BRACKET_CLOSE, ')', substr($str,1));
		elseif (substr($str,0,1) == ',') return array(self::TOKEN_COMMA, ',', substr($str,1));
		elseif (ctype_digit(substr($str,0,1))) {
			$res = '';
			while(ctype_digit(substr($str,0,1))) {
				$res .= substr($str,0,1);
				$str = substr($str,1);
			}
			return array(self::TOKEN_NUMBER, $res, $str);
		}
		elseif (self::charIsIDChar(substr($str,0,1))) {
			$res = '';
			while(self::charIsIDChar(substr($str,0,1)) || ctype_digit(substr($str,0,1)) || (substr($str,0,1) == '.')) {
				$res .= substr($str,0,1);
				$str = substr($str,1);
			}
			
			if (strtolower($res) == 'and') return array(self::TOKEN_OPERATOR_BINARY,'&&',$str);
			elseif (strtolower($res) == 'or') return array(self::TOKEN_OPERATOR_BINARY,'||',$str);
			elseif (strtolower($res) == 'not') return array(self::TOKEN_OPERATOR_UNARY,'!',$str);
			else return array(self::TOKEN_ID, $res, $str);
		}
		elseif (substr($str,0,1) == '"' || substr($str,0,1) == '\'') {
			$res = '';
			$delim = substr($str,0,1);
			$str = substr($str,1);
			
			while(true) {
				if (substr($str,0,2) == '\\"') {
					$res .= '"';
					$str = substr($str,2);
					continue;
				}
				if (substr($str,0,2) == '\\\'') {
					$res .= '\'';
					$str = substr($str,2);
					continue;
				}
				
				$ch = substr($str,0,1);
				$str = substr($str,1);

				if ($ch == $delim) {
					break;
				}
				
				$res .= $ch;
			}
			
			return array(self::TOKEN_STRING, $res, $str);
		}
		else {
			throw new ExpressionParser_Exception(sprintf('Unknown token near "%s"', substr($str,-5,10)));
		}
	}
	
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @return ExpressionParser_Tokenizer
	 */
	public static function Tokenize($str) {
		$str = trim($str);
		if (strlen($str) == 0) return null;
		
		$obj = new ExpressionParser_Tokenizer();
		
		while(true) {
			list($tokenType, $tokenValue, $retStr) = self::nextToken($str);
			$str = $retStr;
			if ($tokenType == self::TOKEN_NONE) break;
			
			$obj->tokens[] = array(
				'type' => $tokenType,
				'value' => $tokenValue,
			);
		}
		
		return $obj;
	}
	
}

?>