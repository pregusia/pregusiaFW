<?php

class DateAndTimeRange implements JsonSerializable {
	
	private $oStart = null;
	private $oStop = null;
	
	//************************************************************************************
	/**
	 * @return DateAndTime
	 */
	public function getStart() { return $this->oStart; }

	//************************************************************************************
	/**
	 * @return DateAndTime
	 */
	public function getStop() { return $this->oStop; }
 
	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * Czy zawiera podana date
	 * @param DateAndTime $arg
	 * @param Date $arg
	 */
	public function contains($arg) {
		$ts = 0;
		if ($arg instanceof DateAndTime) $ts = $arg->getTimestamp();
		elseif ($arg instanceof Date) $ts = $arg->getTimestamp();
		else throw new InvalidArgumentException('arg is not DateAndTime nor Date');
		
		return $ts >= $this->getStart()->getTimestamp()
			&& $ts <= $this->getStop()->getTimestamp();
	}	
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'start' => $this->getStart()->toString(),
			'stop' => $this->getStop()->toString()	
		);
	}
	
	//************************************************************************************
	public function toString() {
		return sprintf('from %s to %s', $this->getStart()->toString(), $this->getStop()->toString());
	}
	
	//************************************************************************************
	/**
	 * @return DatesRange
	 */
	public function toDatesRange() {
		return DatesRange::CreateFromRange($this->getStart()->getDate(), $this->getStop()->getDate());
	}
	
	//************************************************************************************
	/**
	 * @param DateAndTime $oStart
	 * @param DateAndTime $oStop
	 * @return DateAndTimeRange
	 */
	public static function CreateFromRange($oStart, $oStop) {
		if (!($oStart instanceof DateAndTime)) throw new InvalidArgumentException('Start is not DateAndTime');
		if (!($oStop instanceof DateAndTime)) throw new InvalidArgumentException('Stop is not DateAndTime');
		if ($oStart->getTimestamp() > $oStop->getTimestamp()) {
			$tmp = $oStart;
			$oStart = $oStop;
			$oStop = $tmp;
		}
		
		$obj = new DateAndTimeRange();
		$obj->oStart = $oStart;
		$obj->oStop = $oStop;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param DateAndTime $oStart
	 * @param DateShift $oShift
	 * @return DatesRange
	 */
	public static function CreateFromShift($oStart, $oShift) {
		if (!($oStart instanceof Date)) throw new InvalidArgumentException('Start is not DateAndTime');
		if (!($oShift instanceof DateShift)) throw new InvalidArgumentException('Shift is not DateShift');
		
		$oStop = $oStart->Add($oShift);
		return self::CreateFromRange($oStart, $oStop);
	}
	
	//************************************************************************************
	public static function jsonUnserialize($arr) {
		if ($arr['start'] && $arr['stop']) {
			$oStart = DateAndTime::FromString($arr['start']);
			$oStop = DateAndTime::FromString($arr['stop']);
			return self::CreateFromRange($oStart, $oStop);
		} else {
			return null;
		}
	}	
	
}

?>