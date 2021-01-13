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


class MetaStringToken {

	private static $EMPTY = false;
	
	const TYPE_NONE = 0;
	const TYPE_RAW = 1;
	const TYPE_TAG_OPEN = 2;
	const TYPE_TAG_CLOSE = 3;
	const TYPE_TAG_SINGLE = 4;
	
	private $type = 0;
	private $tagName = '';
	private $tagParam = '';
	private $text = '';
	private $rawText = '';
	
	//************************************************************************************
	public function getType() { return $this->type; }
	public function getTagName() { return $this->tagName; }
	public function getTagParam() { return $this->tagParam; }
	public function getText() { return $this->text; }
	public function getRawText() { return $this->rawText; }

	//************************************************************************************
	public function __construct($rawText, $type, $text, $tagName, $tagParam) {
		$this->type = $type;
		$this->text = $text;
		$this->tagName = $tagName;
		$this->tagParam = $tagParam;
		$this->rawText = $rawText;
	}
	
	//************************************************************************************
	/**
	 * @return MetaStringToken
	 */
	public static function CreateEmpty() {
		if (self::$EMPTY === false) {
			self::$EMPTY = new MetaStringToken('', self::TYPE_NONE, '', '', '');
		}
		return self::$EMPTY;
	}
	
}

?>