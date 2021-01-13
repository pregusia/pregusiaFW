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
 * Zawiera nierozwiniety zakres dat
 * Rozwija go jesli jest taka potrzeba
 * @author pregusia
 *
 */
class DatesRange implements IteratorAggregate, JsonSerializable {
	
	private $oStart = null;
	private $oStop = null;
	
	/**
	 * @var DatesCollection
	 */
	private $days = null;
	
	//************************************************************************************
	/**
	 * @return Date
	 */
	public function getStart() { return $this->oStart; }

	//************************************************************************************
	/**
	 * @return Date
	 */
	public function getStop() { return $this->oStop; }
 
	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * @return Date[]
	 */
	public function getDays() {
		if ($this->days == null) $this->days = DatesCollection::ExpandRange($this->getStart(), $this->getStop());
		return $this->days->getDays();
	}
	
	//************************************************************************************
	public function getCount() {
		$d = $this->getStop()->getTimestamp() - $this->getStart()->getTimestamp() + 3600 * 24;
		return intval($d / (3600 * 24));
	}
	
	//************************************************************************************
	public function getIterator() {
		if ($this->days == null) $this->days = DatesCollection::ExpandRange($this->getStart(), $this->getStop());
		return $this->days->getIterator();
	}	

	//************************************************************************************
	/**
	 * Czy zawiera podana date
	 * @param Date $arg
	 * @param DateAndTime $arg
	 */
	public function contains($arg) {
		$ts = 0;
		if ($arg instanceof DateAndTime) $ts = $arg->getDate()->getTimestamp();
		elseif ($arg instanceof Date) $ts = $arg->getTimestamp();
		else throw new InvalidArgumentException('arg is not DateAndTime nor Date');
				
		return $ts >= $this->getStart()->getTimestamp()
			&& $ts <= $this->getStop()->getTimestamp();
	}	
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'start' => $this->getStart()->toString(),
			'stop' => $this->getStop()->toString(),	
		);
	}
	
	//************************************************************************************
	public function toString() {
		return sprintf('from %s to %s', $this->getStart()->toString(), $this->getStop()->toString());
	}
	
	//************************************************************************************
	/**
	 * @return DateAndTimeRange
	 */
	public function toDateAndTimeRange() {
		return DateAndTimeRange::CreateFromRange($this->getStart()->toDateAndTime(0, 0, 0), $this->getStop()->toDateAndTime(23, 59, 59));
	}
	
	//************************************************************************************
	/**
	 * @param Date $oStart
	 * @param Date $oStop
	 * @return DatesRange
	 */
	public static function CreateFromRange($oStart, $oStop) {
		if (!($oStart instanceof Date)) throw new InvalidArgumentException('Start is not Date');
		if (!($oStop instanceof Date)) throw new InvalidArgumentException('Stop is not Date');
		if ($oStart->getTimestamp() > $oStop->getTimestamp()) {
			$tmp = $oStart;
			$oStart = $oStop;
			$oStop = $tmp;
		}
		
		$obj = new DatesRange();
		$obj->oStart = $oStart;
		$obj->oStop = $oStop;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param Date $oStart
	 * @param DateShift $oShift
	 * @return DatesRange
	 */
	public static function CreateFromShift($oStart, $oShift) {
		if (!($oStart instanceof Date)) throw new InvalidArgumentException('Start is not Date');
		if (!($oShift instanceof DateShift)) throw new InvalidArgumentException('Shift is not DateShift');
		
		$oStop = $oStart->Add($oShift);
		return self::CreateFromRange($oStart, $oStop);
	}
	
	//************************************************************************************
	public static function jsonUnserialize($arr) {
		if ($arr['start'] && $arr['stop']) {
			$oStart = Date::FromString($arr['start']);
			$oStop = Date::FromString($arr['stop']);
			return self::CreateFromRange($oStart, $oStop);
		} else {
			return null;
		}
	}

}

?>