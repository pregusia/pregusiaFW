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


class MetaStringTokensCollection {
	
	/**
	 * @var MetaStringToken[]
	 */
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
	public function isNext($type) {
		if (!$this->tokens[$this->position]) {
			return $type == MetaStringToken::TYPE_NONE;
		} else {
			return $this->tokens[$this->position]->getType() == $type;
		}
	}
	
	//************************************************************************************
	/**
	 * @return MetaStringToken
	 */
	public function popNext() {
		if ($this->tokens[$this->position]) {
			$oToken = $this->tokens[$this->position];
			$this->position += 1;
		} else {
			$oToken = MetaStringToken::CreateEmpty();
		}
		return $oToken;
	}
	
	//************************************************************************************
	/**
	 * 
	 * @param string $str
	 * @return BBCodeTokensCollection
	 */
	public static function Tokenize($str) {
		$obj = new MetaStringTokensCollection();
		$str = trim($str);
		if (!$str) return $obj;
		
		$arr = preg_split('/(\[\/?[A-Za-z0-9\.\-]+(?:=[A-Za-z0-9\#\.\-]+)?\/?\])/',$str, null, PREG_SPLIT_DELIM_CAPTURE);
		foreach($arr as $v) {
			if (!$v) continue;
			$rawText = $v;
			
			if (substr($v,0,2) == '[/') {
				$tmp = trim($v,' [/]');
				$obj->tokens[] = new MetaStringToken($rawText, MetaStringToken::TYPE_TAG_CLOSE, '', $tmp, '');
				continue;
			}
			if (substr($v,0,1) == '[' && substr($v,-2) == '/]') {
				$tmp = trim($v,' []/');
				
				if (strpos($tmp, '=') !== false) {
					list($a, $b) = explode('=',$tmp,2);
					$obj->tokens[] = new MetaStringToken($rawText, MetaStringToken::TYPE_TAG_SINGLE, '', $a, $b);
				} else {
					$obj->tokens[] = new MetaStringToken($rawText, MetaStringToken::TYPE_TAG_SINGLE, '', $tmp, '');
				}
				continue;
			}
			if (substr($v,0,1) == '[') {
				$tmp = trim($v,' []');
				
				if (strpos($tmp, '=') !== false) {
					list($a, $b) = explode('=',$tmp,2);
					$obj->tokens[] = new MetaStringToken($rawText, MetaStringToken::TYPE_TAG_OPEN, '', $a, $b);
				} else {
					$obj->tokens[] = new MetaStringToken($rawText, MetaStringToken::TYPE_TAG_OPEN, '', $tmp, '');
				}
				
				continue;				
			}
			$obj->tokens[] = new MetaStringToken($rawText, MetaStringToken::TYPE_RAW, $v, '', '');
		}
		
		return $obj;
	}
	
}

?>