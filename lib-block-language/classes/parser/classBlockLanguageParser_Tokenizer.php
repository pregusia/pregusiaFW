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

class BlockLanguageParser_Tokenizer {
	
	const TOKEN_NONE = 0;
	const TOKEN_IGNORE = -1;
	
	const TOKEN_ID = 1;
	const TOKEN_NUMBER = 2;
	const TOKEN_STRING = 3;
	
	const TOKEN_BLOCK_OPEN = 10;
	const TOKEN_BLOCK_CLOSE = 11;
	
	const TOKEN_SEMICOLON = 70; // ;
	const TOKEN_COLON = 71; // :
	const TOKEN_COMMA = 72; // ,
	
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
	public function peekNextTokenType() {
		if ($this->tokens[$this->position]) {
			return $this->tokens[$this->position]['type'];
		}
		return self::TOKEN_NONE;
	}
	
	//************************************************************************************
	private static function charIsIDChar($ch) {
		return ctype_alpha($ch) || $ch == '-' || $ch == '_';
		
	}
	
	//************************************************************************************
	private static function nextToken($str) {
		$str = trim($str);
		if (strlen($str) == 0) return array(self::TOKEN_NONE,'','');
		
		if (mb_substr($str,0,2) == '//' || mb_substr($str,0,1) == '#') {
			$p = mb_strpos($str, "\n");
			if ($p !== false) {
				return array(self::TOKEN_IGNORE, '', substr($str, $p));
			} else {
				return array(self::TOKEN_NONE,'','');
			}
		}
		
		elseif (mb_substr($str,0,1) == '{') return array(self::TOKEN_BLOCK_OPEN, '{', mb_substr($str,1));
		elseif (mb_substr($str,0,1) == '}') return array(self::TOKEN_BLOCK_CLOSE, '}', mb_substr($str,1));
		elseif (mb_substr($str,0,1) == ':') return array(self::TOKEN_COLON, ':', mb_substr($str,1));
		elseif (mb_substr($str,0,1) == ',') return array(self::TOKEN_COMMA, ',', mb_substr($str,1));
		elseif (mb_substr($str,0,1) == ';') return array(self::TOKEN_SEMICOLON, ';', mb_substr($str,1));
		elseif (ctype_digit(mb_substr($str,0,1))) {
			$res = '';
			while(ctype_digit(mb_substr($str,0,1))) {
				$res .= mb_substr($str,0,1);
				$str = mb_substr($str,1);
			}
			return array(self::TOKEN_NUMBER, $res, $str);
		}
		elseif (self::charIsIDChar(mb_substr($str,0,1))) {
			$res = '';
			while(self::charIsIDChar(mb_substr($str,0,1)) || ctype_digit(mb_substr($str,0,1)) || (mb_substr($str,0,1) == '.')) {
				$res .= mb_substr($str,0,1);
				$str = mb_substr($str,1);
			}
			
			return array(self::TOKEN_ID, $res, $str);
		}
		elseif (mb_substr($str,0,1) == '"' || mb_substr($str,0,1) == '\'') {
			$res = '';
			$delim = mb_substr($str,0,1);
			$str = mb_substr($str,1);
			
			while(true) {
				if (mb_substr($str,0,2) == '\\"') {
					$res .= '"';
					$str = mb_substr($str,2);
					continue;
				}
				if (mb_substr($str,0,2) == '\\\'') {
					$res .= '\'';
					$str = mb_substr($str,2);
					continue;
				}
				
				$ch = mb_substr($str,0,1);
				$str = mb_substr($str,1);

				if ($ch == $delim) {
					break;
				}
				
				$res .= $ch;
			}
			
			return array(self::TOKEN_STRING, $res, $str);
		}
		else {
			throw new BlockLanguageParser_Exception(sprintf('Unknown token near "%s"', substr($str,-5,10)));
		}
	}
	
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @return BlockLanguageParser_Tokenizer
	 */
	public static function Tokenize($str) {
		$str = trim($str);
		if (strlen($str) == 0) return null;
		
		$obj = new BlockLanguageParser_Tokenizer();
		
		while(true) {
			list($tokenType, $tokenValue, $retStr) = self::nextToken($str);
			$str = $retStr;
			if ($tokenType == self::TOKEN_IGNORE) continue;
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