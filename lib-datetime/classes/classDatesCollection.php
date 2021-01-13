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


class DatesCollection implements IteratorAggregate {
	
	private $days = array();
	private $limitsDirty = false;
	private $oMinimum = null;
	private $oMaximum = null;
	
	//************************************************************************************
	public function __construct() {
		
	}
	
	//************************************************************************************
	public function getCount() {
		return count($this->days);
	}
	
	//************************************************************************************
	/**
	 * @return DatesCollection
	 */
	public function getCopy() {
		$obj = new DatesCollection();
		foreach($this->days as $k => $v) $obj->days[$k] = $v;
		$obj->limitsDirty = $this->limitsDirty;
		$obj->oMaximum = $this->oMaximum;
		$obj->oMinimum = $this->oMinimum;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * Zwraca najmniejsza date w kolekcji
	 * @return Date
	 */
	public function getMinimum() {
		if ($this->limitsDirty) $this->sort();
		return $this->oMinimum;
	}
	
	//************************************************************************************
	/**
	 * Zwraca najwieksza date w kolekcji
	 * @return Date
	 */
	public function getMaximum() {
		if ($this->limitsDirty) $this->sort();
		return $this->oMaximum;
	}	
	
	//************************************************************************************
	/**
	 * @return Date[]
	 */
	public function getDays() {
		return $this->days;
	}
	
	//************************************************************************************
	public function getIterator() {
		return new ArrayIterator($this->days);
	}
	
	//************************************************************************************
	/**
	 * Zwraca daty z miesiecy ktore obejmuje ta kolekcja
	 * @return Generator
	 */
	public function iterateMonths() {
		$used = array();
		foreach($this->days as $oDate) {
			false && $oDate = new Date();
			$k = $oDate->getMonth() . '-' . $oDate->getYear();
			if (!$used[$k]) {
				yield $oDate;
				$used[$k] = 1;
			}
		}
	}
	
	//************************************************************************************
	/**
	 * Dodaje do kolekcji
	 * @param Date | DatesCollection | DatesRange | DateShift $arg
	 * @return int Liczba dni ktore sie dodaly
	 * @throws InvalidArgumentException
	 */
	public function add($arg) {
		if ($arg instanceof Date) {
			if (!$this->days[$arg->getTimestamp()]) {
				$this->days[$arg->getTimestamp()] = $arg;
				$this->limitsDirty = true;
				return 1;
			} else {
				return 0;
			}
		}
		elseif (($arg instanceof DatesRange) || ($arg instanceof DatesCollection)) {
			$n = 0;
			foreach($arg as $oDate) {
				$n += $this->add($oDate);
			}
			return $n;
		}
		elseif ($arg instanceof DateShift) {
			// jest to przesuniecie, znaczy ze musimy je dodac od ostaniego dnia
			$oMax = $this->getMaximum();
			if (!$oMax) $oMax = Date::Now();
			
			$oRange = DatesRange::CreateFromShift($oMax, $arg);
			return $this->add($oRange);
		}
		else {
			throw new InvalidArgumentException('Arg is not neither Date/DatesRange/DatesCollection/DateShift');
		}
	}
	
	//************************************************************************************
	public function isEmpty() {
		return count($this->days) == 0;
	}
	
	//************************************************************************************
	/**
	 * Dodaje do kolekcji
	 * @param Date | DatesCollection | DatesRange $oDate
	 * @throws InvalidArgumentException
	 */
	public function remove($arg) {
		if ($arg instanceof Date) {
			unset($this->days[$arg->getTimestamp()]);
			$this->limitsDirty = true;
		}
		elseif (($oDate instanceof DatesRange) || ($oDate instanceof DatesCollection)) {
			foreach($arg as $oDate) {
				$this->remove($oDate);
			}
		}
		else {
			throw new InvalidArgumentException();
		}
	}
	
	//************************************************************************************
	/**
	 * Zwraca zakresy dat pokrywajace ta kolekcje
	 * @return DatesRange[]
	 */
	public function getRanges() {
		if (empty($this->days)) return array();
		
		$ranges = array();
		$this->sort();
		
		$oI = $this->getMinimum();
		while(UtilsComparable::isLessOrEqual($oI, $this->getMaximum())) {
			if ($this->contains($oI)) {
				$oStart = $oI;
				
				while($this->contains($oI)) {
					$oI = $oI->Add(DateShift::Create(DateUnit::DAY, 1));
				}
				
				$oStop = $oI->Add(DateShift::Create(DateUnit::DAY, -1));
				
				$ranges[] = DatesRange::CreateFromRange($oStart, $oStop);
			} else {
				$oI = $oI->Add(DateShift::Create(DateUnit::DAY, 1));
			}
		}
		
		return $ranges;		
	}

	//************************************************************************************
	/**
	 * Sprawdza czy ta data zawiera sie w tej kolekcji
	 * @param Date $oDate
	 * @throws InvalidArgumentException
	 */
	public function contains($oDate) {
		if (!($oDate instanceof Date)) throw new InvalidArgumentException();
		return isset($this->days[$oDate->getTimestamp()]);
	}

	//************************************************************************************
	private function sort() {
		$this->limitsDirty = false;
		
		if (empty($this->days)) {
			$this->oMinimum = null;
			$this->oMaximum = null;
			return;
		}
		
		ksort($this->days);
		
		$minTs = $maxTs = false;
		foreach($this->days as $k => $v) {
			if ($k < $minTs || $minTs === false) $minTs = $k;
			if ($k > $maxTs || $maxTs === false) $maxTs = $k;
		}		

		$this->oMinimum = Date::FromTimestamp($minTs);
		$this->oMaximum = Date::FromTimestamp($maxTs);
	}
	
	//************************************************************************************
	/**
	 * @param Date $oStart
	 * @param Date $oStop
	 * @return DatesCollection
	 */
	public static function ExpandRange($oStart, $oStop) {
		if (!($oStart instanceof Date)) throw new InvalidArgumentException('Start is not Date');
		if (!($oStop instanceof Date)) throw new InvalidArgumentException('Stop is not Date');
		if ($oStart->getTimestamp() > $oStop->getTimestamp()) {
			$tmp = $oStart;
			$oStart = $oStop;
			$oStop = $tmp;
		}
		
		$obj = new DatesCollection();
		
		$num = 0;
		$oI = $oStart;
		while(true) {
			$obj->Add($oI);

			if ($oI->compareTo($oStop) == 0) break;
			
			$oI = $oI->Add(DateShift::Create(DateUnit::DAY, 1));

			$num += 1;
			if ($num > 5000) throw new TooManyDaysException(5000);
		}
		
		return $obj;		
	}
	
}

?>