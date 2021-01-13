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


/**
 * Reprezentuje przesuniecie
 * Posiada wartosc oraz jednostke
 * @author pregusia
 *
 */
class DateShift implements JsonSerializable {
	
	private $unit = 0;
	private $value = 0;

	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	public function getUnit() { return $this->unit; }
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	public function isZero() { return $this->value == 0; }
	
	//************************************************************************************
	/**
	 * Zamienia przesuniecie na przeciwne
	 * @return DateShift
	 */
	public function Neg() {
		$obj = new DateShift();
		$obj->unit = $this->unit;
		$obj->value = $this->value * (-1);
		return $obj;
	}
	
	//************************************************************************************
	public function toString() {
		return sprintf('%d%s', $this->value, DateUnit::getInstance()->toRawString($this->unit));
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'unit' => $this->unit,
			'value' => $this->value	
		);
	}
	
	//************************************************************************************
	/**
	 * Normalizuje przesuniecia do przesuniecia w dniach
	 * przyjmuje ze miesiac ma 30 dni a rok 365
	 * 
	 * @return DateShift
	 */
	public function normalizeToDays() {
		if ($this->unit == DateUnit::MONTH) {
			return self::Create(DateUnit::DAY, $this->value * 30);
		}
		elseif ($this->unit == DateUnit::YEAR) {
			return self::Create(DateUnit::DAY, $this->value * 365);
		}
		elseif ($this->unit == DateUnit::WEEK) {
			return self::Create(DateUnit::DAY, $this->value * 7);
		}
		else {
			return $this;
		}
	}
	
	
	//************************************************************************************
	public static function CreateFromString($str) {
		$str = trim($str);
		if (!$str) return null;
		
		$u = strtolower(substr($str,-1));
		$v = intval(substr($str,0,strlen($str) - 1));
		
		$obj = new DateShift();
		$obj->value = $v;
		$obj->unit = DateUnit::FromStr($u);
		return $obj;		
	}
	
	//************************************************************************************
	private static $empty = null;
	/**
	 * @return DateShift
	 */
	public static function CreateEmpty() {
		if (!self::$empty) self::$empty = self::Create(DateUnit::DAY, 0);
		return self::$empty;
	}
	
	//************************************************************************************
	public static function Create($unit, $value) {
		$value = intval($value);
		if (!DateUnit::getInstance()->isValid($unit)) throw new InvalidArgumentException('Invalid DateShift unit');
		
		$obj = new DateShift();
		$obj->value = $value;
		$obj->unit = $unit;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return DateShift
	 */
	public static function jsonUnserialize($arr) {
		if ($arr['unit'] && $arr['value']) {
			return self::Create($arr['unit'], $arr['value']);
		} else {
			return self::CreateEmpty();
		}
	}
	
	//************************************************************************************
	/**
	 * Zwraca przesuniecie w dniach od $oStart do $oStop
	 * @param Date $oStart
	 * @param Date $oStop
	 * @return DateShift
	 */
	public static function CreateFromDates($oStart, $oStop) {
		if (!($oStart instanceof Date)) throw new InvalidArgumentException('Start is not Date');
		if (!($oStop instanceof Date)) throw new InvalidArgumentException('Stop is not Date');
		
		$ts1 = $oStart->getTimestamp();
		$ts2 = $oStop->getTimestamp();
		$days = ($ts2 - $ts1) / (3600 * 24);
		
		return self::Create(DateUnit::DAY, $days);
	}
	
}

?>