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


class Month {
	
	//************************************************************************************
	public static function getMaxDays($year, $month) {
		$ts = mktime(12,0,0,$month, 5, $year);
		return intval(date('t', $ts));
	}
	
	//************************************************************************************
	/**
	 * Zwraca zakres od 1 do ostatniego danego miesiaca
	 * @param int $year
	 * @param int $month
	 * @return DatesRange
	 */
	public static function getRange($year, $month) {
		return DatesRange::CreateFromRange(
			new Date(1, $month, $year),
			new Date(self::getMaxDays($year, $month), $month, $year)
		);
	}
	
	//************************************************************************************
	public static function getPolishName($month) {
		switch($month) {
			case 1: return 'styczeń';
			case 2: return 'luty';
			case 3: return 'marzec';
			case 4: return 'kwiecień';
			case 5: return 'maj';
			case 6: return 'czerwiec';
			case 7: return 'lipiec';
			case 8: return 'sierpień';
			case 9: return 'wrzesień';
			case 10: return 'październik';
			case 11: return 'listopad';
			case 12: return 'grudzień';
			default: return sprintf('Unknown - %d', $month);
		}
	}
	
}

?>