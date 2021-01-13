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


class Time implements IComparable, JsonSerializable {
	
	private $hour = 0;
	private $min = 0;
	private $sec = 0;
	
	//************************************************************************************
	public function getHour() { return $this->hour; }
	public function getMinutes() { return $this->min; }
	public function getSeconds() { return $this->sec; }
	
	//************************************************************************************
	public function getHashCode() {
		return $this->hour * 3600 + $this->min * 60 + $this->sec;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $arg
	 * @return int
	 */
	public function compareTo($arg) {
		if (!($arg instanceof Time)) throw new InvalidArgumentException('arg is not Time');
		return $this->getHashCode() - $arg->getHashCode();
	}
	
	//************************************************************************************
	public function __construct($hour, $min, $sec) {
		$this->hour = UtilsNumber::clamp($hour, 0, 23);
		$this->min = UtilsNumber::clamp($min, 0, 59);
		$this->sec = UtilsNumber::clamp($sec, 0, 59);
	}
	
	//************************************************************************************
	/**
	 * @param int $s
	 * @return Time
	 */
	public function AddSeconds($s) {
		$hash = ($this->getHashCode() + $s) % (24 * 3600);
		while ($hash < 0) $hash += 24 * 3600;
		$rs = $hash % 60;
		
		$hash = ($hash - $rs) / 60;
		$rm = $hash % 60;
		
		$hash = ($hash - $rm) / 60;
		$rh = $hash % 24; 
		
		return new Time($rh, $rm, $rs);
	}
	
	//************************************************************************************
	/**
	 * @param int $m
	 * @return Time
	 */
	public function AddMinutes($m) {
		return $this->AddSeconds($m * 60);
	}
	
	//************************************************************************************
	/**
	 * @param int $h
	 * @return Time
	 */
	public function AddHours($h) {
		return $this->AddMinutes($h * 60);
	}

	//************************************************************************************
	public function toString() { return sprintf('%02d:%02d:%02d', $this->hour, $this->min, $this->sec); }
	
	//************************************************************************************
	/**
	 * @return Time
	 */
	public static function Now() {
		return self::FromTimestamp(ApplicationContext::getCurrent()->getTimestamp());
	}
	
	//************************************************************************************
	/**
	 * @param int $ts
	 * @return Time
	 */
	public static function FromTimestamp($ts) {
		$h = date('G', $ts);
		$m = date('i', $ts);
		$s = date('s', $ts);
		return new Time($h, $m, $s);
	}

	//************************************************************************************
	/**
	 * @return Time
	 */
	public static function FromString($str) {
		list($h,$m,$s) = explode(':', $str);
		return new Time($h, $m, $s);
	}

	//************************************************************************************
	public function jsonSerialize() {
		return $this->toString();
	}
	
	//************************************************************************************
	/**
	 * @param unknown $v
	 * @return Time
	 */
	public static function jsonUnserialize($v) {
		if ($v) {
			return self::FromString($v);
		} else {
			return null;
		}
	}
	
}

?>