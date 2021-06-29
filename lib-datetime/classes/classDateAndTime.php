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


class DateAndTime implements IComparable, JsonSerializable {
	
	/**
	 * @var Date
	 */
	private $oDate = null;
	
	/**
	 * @var Time
	 */
	private $oTime = null;
	
	//************************************************************************************
	/**
	 * @return Date
	 */
	public function getDate() { return $this->oDate; }
	
	//************************************************************************************
	/**
	 * @return Time
	 */
	public function getTime() { return $this->oTime; }
	
	//************************************************************************************
	public function getDay() { return $this->getDate()->getDay(); }
	public function getMonth() { return $this->getDate()->getMonth(); }
	public function getYear() { return $this->getDate()->getYear(); }

	//************************************************************************************
	public function getHour() { return $this->getTime()->getHour(); }
	public function getMinutes() { return $this->getTime()->getMinutes(); }
	public function getSeconds() { return $this->getTime()->getSeconds(); }
	
	//************************************************************************************
	/**
	 * @param mixed $arg
	 * @return int
	 */
	public function compareTo($arg) {
		if (!($arg instanceof DateAndTime)) throw new InvalidArgumentException('arg is not DateAndTime');
		$r = $this->getDate()->compareTo($arg->getDate());
		if ($r != 0) return $r;
		return $this->getTime()->compareTo($arg->getTime());
	}
	
	//************************************************************************************
	public function getTimestamp() { return mktime($this->getHour(),$this->getMinutes(),$this->getSeconds(),$this->getMonth(), $this->getDay(), $this->getYear()); }

	//************************************************************************************
	public function __construct($oDate, $oTime) {
		if (!($oDate instanceof Date)) throw new InvalidArgumentException('oDate is not Date');
		if (!($oTime instanceof Time)) throw new InvalidArgumentException('oTime is not Time');
		$this->oDate = $oDate;
		$this->oTime = $oTime;
	}
	
	//************************************************************************************
	/**
	 * @param DateShift $oShift
	 * @return DateAndTime
	 */
	public function Add($oShift) {
		$oNewDate = $this->getDate()->Add($oShift);
		return new DateAndTime($oNewDate, $this->oTime);
	}
	
	//************************************************************************************
	/**
	 * @param int $s
	 * @return DateAndTime
	 */
	public function AddSeconds($s) {
		return self::FromTimestamp($this->getTimestamp() + $s);
	}
	
	//************************************************************************************
	/**
	 * @param int $m
	 * @return DateAndTime
	 */
	public function AddMinutes($m) {
		return self::FromTimestamp($this->getTimestamp() + $m * 60);
	}

	//************************************************************************************
	/**
	 * @param int $h
	 * @return DateAndTime
	 */
	public function AddHours($h) {
		return self::FromTimestamp($this->getTimestamp() + $h * 3600);
	}
	
	//************************************************************************************
	public function toString() { return sprintf('%s %s', $this->getDate()->toString(), $this->getTime()->toString()); }
	
	//************************************************************************************
	/**
	 * Zwraca date w formacie ISO, tj. np. 2021-05-11T09:54:13.000000
	 * @return string
	 */
	public function toISOString() {
		return sprintf('%sT%s.000000', $this->getDate()->toString(), $this->getTime()->toString());
	}
	
	//************************************************************************************
	/**
	 * @return DateAndTime
	 */
	public static function Now() {
		return self::FromTimestamp(ApplicationContext::getCurrent()->getTimestamp());
	}
	
	//************************************************************************************
	/**
	 * @param int $ts
	 * @return DateAndTime
	 */
	public static function FromTimestamp($ts) {
		return new DateAndTime(Date::FromTimestamp($ts), Time::FromTimestamp($ts));
	}

	//************************************************************************************
	/**
	 * @return DateAndTime
	 */
	public static function FromString($str) {
		$arr = explode(' ',$str,2);
		if (count($arr) != 2) {
			$arr = explode('T',$str,2);
		}
		if (count($arr) != 2) return null;
		
		$oDate = Date::FromString($arr[0]);
		$oTime = Time::FromString($arr[1]);
		if ($oDate && $oTime) {
			return new DateAndTime($oDate, $oTime);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return $this->toString();
	}
	
	//************************************************************************************
	/**
	 * @param string $v
	 * @return DateAndTime
	 */
	public static function jsonUnserialize($v) {
		if ($v === null) return null;
		return self::FromString($v);
	}
	
}

?>