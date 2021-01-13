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


class NameValueCaptionTriple implements JsonSerializable {

	private $name = '';
	private $value = '';
	private $caption = '';
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function getValue() { return $this->value; }
	public function getCaption() { return $this->caption; }
	
	//************************************************************************************
	public function __construct($name, $value, $caption) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		$this->name = $name;
		
		$caption = trim($caption);
		if (!$caption) throw new InvalidArgumentException('Empty caption');
		$this->caption = $caption;
		
		$this->value = $value;
	}
	
	//************************************************************************************
	/**
	 * @return NameValuePair
	 */
	public function getNameValuePair() {
		return new NameValuePair($this->name, $this->value);
	}
	
	//************************************************************************************
	/**
	 * @return NameCaptionPair
	 */
	public function getNameCaptionPair() {
		return new NameCaptionPair($this->name, $this->caption);
	}
	
	//************************************************************************************
	public function tplRender($key,$oContext) {
		if ($key == 'name') return htmlspecialchars($this->name);
		if ($key == 'value') return htmlspecialchars($this->value);
		if ($key == 'caption') return htmlspecialchars($this->caption);
		return '';
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'name' => $this->name,
			'value' => $this->value,
			'caption' => $this->caption
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return NameValueCaptionTriple
	 */
	public static function jsonUnserialize($arr) {
		if ($arr['name']) {
			return new NameValueCaptionTriple($arr['name'], $arr['value'], $arr['caption']);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return NameValueCaptionTriple[]
	 */
	public static function jsonUnserializeArray($arr) {
		$res = array();
		foreach($arr as $v) {
			if ($obj == self::jsonUnserialize($v)) {
				$res[] = $obj;
			}
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair $oPair
	 * @param string $caption
	 * @return NameValueCaptionTriple
	 */
	public static function CreateFromValue($oPair, $caption) {
		if (!($oPair instanceof NameValuePair)) throw new InvalidArgumentException('oPair is not NameValuePair');
		return new NameValueCaptionTriple($oPair->getName(), $oPair->getValue(), $caption);
	}
	
	//************************************************************************************
	/**
	 * @param NameCaptionPair $oPair
	 * @param string $value
	 * @return NameValueCaptionTriple
	 */
	public static function CreateFromCaption($oPair, $value) {
		if (!($oPair instanceof NameCaptionPair)) throw new InvalidArgumentException('oPair is not NameCaptionPair');
		return new NameValueCaptionTriple($oPair->getName(), $value, $oPair->getCaption());
	}
	
	//************************************************************************************
	/**
	 * Sumuje dwa zbiory
	 * 
	 * @param NameValuePair[] $values
	 * @param NameCaptionPair[] $captions
	 * @return NameValueCaptionTriple[]
	 */
	public static function Join($values, $captions) {
		$arr = array();
		
		foreach($values as $oValue) {
			$arr[$oValue->getName()] = NameValueCaptionTriple::CreateFromValue($oValue, '');
		}
		
		foreach($captions as $oCaption) {
			if ($arr[$oCaption->getName()]) {
				$arr[$oCaption->getName()]->setCaption($oCaption->getCaption());
			} else {
				$arr[$oCaption->getName()] = NameValueCaptionTriple::CreateFromCaption($oCaption, '');
			}
		}
		
		return $arr;
	}
	
}

?>