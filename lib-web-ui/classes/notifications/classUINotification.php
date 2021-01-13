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


class UINotification implements IUIRenderable, JsonSerializable {
	
	use TUIComponentHaving;
	
	const TYPE_SUCCESS = 1;
	const TYPE_ERROR = 2;
	const TYPE_WARNING = 3;
	const TYPE_INFORMATION = 4;
	
	/**
	 * @var ComplexString
	 */
	private $text = '';
	
	private $type = 1;
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getText() { return $this->text; }
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getType() { return $this->type; }
	
	//************************************************************************************
	public function __construct($text, $type=1) {
		$text = ComplexString::AdaptTrim($text);
		if ($text->isEmpty()) throw new InvalidArgumentException('Text is empty');
		
		$this->text = $text;
		$this->type = $type;
	}
	
	//************************************************************************************
	public function getTypeStr() {
		switch($this->type) {
			case self::TYPE_ERROR: return 'error';
			case self::TYPE_INFORMATION: return 'information';
			case self::TYPE_SUCCESS: return 'success';
			case self::TYPE_WARNING: return 'warning';
			default: return '';
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'text': return $this->text;
			case 'type': return $this->getTypeStr();
			default: return '';
		}
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) { return ''; }
	public function uiRenderGetVariableName() { return 'Notification'; }
	
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'type' => $this->type,
			'text' => $this->text->jsonSerialize()
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return UINotification
	 */
	public static function jsonUnserialize($arr) {
		if ($arr['type'] && $arr['text']) {
			return new UINotification(ComplexString::jsonUnserialize($arr['text']), $arr['type']);
		} 
		return null;
	}
	
}

?>