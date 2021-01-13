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

class NotingTagEnumerable implements IEnumerable, JsonSerializable {
	
	private $objectType = array();
	
	/**
	 * @var SQLStorage
	 */
	private $oSQLStorage = null;
	
	//************************************************************************************
	/**
	 * @return SQLStorage
	 */
	public function getSQLStorage() { return $this->oSQLStorage; }
	
	//************************************************************************************
	public function __construct($oSQLStorage, $objectType) {
		if (!($oSQLStorage instanceof SQLStorage)) throw new InvalidArgumentException('oSQLStorage is not SQLStorage');
		$this->oSQLStorage = $oSQLStorage;
		
		if (is_int($objectType)) {
			$this->objectType = array(intval($objectType));
		}
		if (is_array($objectType)) {
			$this->objectType = array_map('intval', $objectType);
		}
	}
	
	//************************************************************************************
	public function enumerableUsageType() { return self::USAGE_SUGGEST; }
	
	//************************************************************************************
	public function enumerableGetAllEnum() {
		return new Enum();
	}
	
	//************************************************************************************
	public function enumerableToString($param) {
		return $param;
	}
	
	//************************************************************************************
	/**
	 * Zwraca sugestie
	 * @param string $text
	 * @return Enum
	 */
	public function enumerableSuggest($text) {
		if (!$this->objectType) return new Enum();
		
		$textSQL = '%' . $text . '%';
		$oEnum = new Enum();
		
		$query = sprintf('SELECT noting_tags.tag FROM noting_tags WHERE noting_tags.objectType IN (%s) AND noting_tags.tag LIKE "%s" LIMIT 10',
			implode(',',$this->objectType),
			$this->getSQLStorage()->escapeString($textSQL)
		);
		foreach($this->getSQLStorage()->getAllFirstColumn($query) as $tag) {
			$oEnum->add($tag, $tag);
		}
		
		return $oEnum;
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'objectType' => $this->objectType,
			'storageName' => $this->getSQLStorage()->getName()	
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return NotingTagEnumerable
	 */
	public static function jsonUnserialize($arr) {
		if (is_array($arr)) {
			$oStorage = ApplicationContext::getCurrent()->getService('SQLStorage', $arr['storageName']);
			false && $oStorage = new SQLStorage();
			
			if ($oStorage) {
				return new NotingTagEnumerable($oStorage, $arr['objectType']);
			}
		}
		return null;
	}
	
}

?>