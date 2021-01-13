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


class NameCaptionPair implements JsonSerializable {
	
	private $name = '';
	private $caption = '';
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function getCaption() { return $this->caption; }
	
	//************************************************************************************
	public function __construct($name, $caption) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		$this->name = $name;
		
		$caption = trim($caption);
		if (!$caption) throw new InvalidArgumentException('Empty caption');
		$this->caption = $caption;
	}
	
	//************************************************************************************
	public function tplRender($key,$oContext) {
		if ($key == 'name') return htmlspecialchars($this->name);
		if ($key == 'caption') return htmlspecialchars($this->caption);
		return '';
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'name' => $this->name,
			'caption' => $this->caption
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return NameCaptionPair
	 */
	public static function jsonUnserialize($arr) {
		if ($arr['name'] && $arr['caption']) {
			return new NameCaptionPair($arr['name'], $arr['caption']);
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return NameCaptionPair[]
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
	
}

?>