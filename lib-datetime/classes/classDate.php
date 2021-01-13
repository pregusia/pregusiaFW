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
 * Przechowuje informacje o pojedynczej dacie
 * Obiekt jest immutable
 * @author pregusia
 *
 */
class Date implements IComparable, JsonSerializable {

	private $day = 0;
	private $month = 0;
	private $year = 0;

	//************************************************************************************
	public function getDay() { return $this->day; }
	public function getMonth() { return $this->month; }
	public function getYear() { return $this->year; }
	public function getTimestamp() { return mktime(12,0,0,$this->getMonth(), $this->getDay(), $this->getYear()); }
	public function getMonthMaxDay() { return Month::getMaxDays($this->year, $this->month); }
	public function getWeekDayNr() { return date('w', $this->getTimestamp()); }
	public function getWeekNr() { return date('W', $this->getTimestamp()); }

	//************************************************************************************
	/**
	 * @param mixed $arg
	 * @return int
	 */
	public function compareTo($arg) {
		if (!($arg instanceof Date)) throw new InvalidArgumentException('arg is not Date');
		return $this->getTimestamp() - $arg->getTimestamp();
	}

	//************************************************************************************
	public function __construct($day, $month, $year) {
		$this->day = intval($day);
		$this->month = intval($month);
		$this->year = intval($year);
		
		if ($this->month < 1) $this->month = 1;
		if ($this->month > 12) $this->month = 12;
		if ($this->day < 1) $this->day = 1;
		if ($this->day > Month::getMaxDays($this->year, $this->month)) {
			$this->day = Month::getMaxDays($this->year, $this->month);
		}
	}

	//************************************************************************************
	/**
	 * @param DateShift $oShift
	 * @return Date
	 */
	public function Add($oShift) {
		if (!($oShift instanceof DateShift)) throw new InvalidArgumentException('oShift is not DateShift');
		if ($oShift->isZero()) return $this;
		
		if ($oShift->getUnit() == DateUnit::DAY) {
			return self::FromTimestamp($this->getTimestamp() + $oShift->getValue() * 3600 * 24);
		}
		
		if ($oShift->getUnit() == DateUnit::WEEK) {
			return self::FromTimestamp($this->getTimestamp() + $oShift->getValue() * 3600 * 24 * 7);
		}
		
		if ($oShift->getUnit() == DateUnit::MONTH) {
			$year = $this->year;
			$month = $this->month;
			$ts = $this->getTimestamp();
			
			for($i=0;$i<abs($oShift->getValue());++$i) {
				$days = Month::getMaxDays($year, $month);
				if ($oShift->getValue() > 0) {
					$ts += $days * 3600 * 24;
					$month += 1;
					if ($month == 13) {
						$year += 1;
						$month = 1;
					}
				} else {
					$ts -= $days * 3600 * 24;
					$month -= 1;
					if ($month == 0) {
						$year -= 1;
						$month = 12;
					}
				}
			}
			
			return self::FromTimestamp($ts);
		}
		
		if ($oShift->getUnit() == DateUnit::YEAR) {
			$year = $this->year;
			$month = $this->month;
			$ts = $this->getTimestamp();
			
			for($i=0;$i<abs($oShift->getValue() * 12);++$i) {
				$days = Month::getMaxDays($year, $month);
				if ($oShift->getValue() > 0) {
					$ts += $days * 3600 * 24;
					$month += 1;
					if ($month == 13) {
						$year += 1;
						$month = 1;
					}
				} else {
					$ts -= $days * 3600 * 24;
					$month -= 1;
					if ($month == 0) {
						$year -= 1;
						$month = 12;
					}
				}
			}
			
			return self::FromTimestamp($ts);
		}		
		
		return null;
	}
	
	//************************************************************************************
	public function toString() {
		return sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
	}
	
	//************************************************************************************
	/**
	 * @param int $hour
	 * @param int $min
	 * @param int $sec
	 * @return DateAndTime
	 */
	public function toDateAndTime($hour, $min, $sec) {
		return new DateAndTime($this, new Time($hour, $min, $sec));
	}

	//************************************************************************************
	/**
	 * @return Date
	 */
	public static function Now() {
		return self::FromTimestamp(ApplicationContext::getCurrent()->getTimestamp());
	}
	
	//************************************************************************************
	/**
	 * @param int $ts
	 * @return Date
	 */
	public static function FromTimestamp($ts) {
		$d = date('j', $ts);
		$m = date('n', $ts);
		$y = date('Y', $ts);
		return new Date($d, $m, $y);
	}

	//************************************************************************************
	/**
	 * @return Date
	 */
	public static function FromString($str) {
		$str = trim($str);
		if (preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/i', $str)) {
			list($y,$m,$d) = explode('-', $str);
			$y = intval($y);
			$m = intval($m);
			$d = intval($d);
			
			if ($y > 1970 && $m >= 1 && $d >= 1) {
				return new Date($d, $m, $y);
			}
		}
		return null;
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return $this->toString();
	}
	
	//************************************************************************************
	/**
	 * @param string $v
	 * @return Date
	 */
	public static function jsonUnserialize($v) {
		if ($v === null) return null;
		return self::FromString($v);
	}

}

?>