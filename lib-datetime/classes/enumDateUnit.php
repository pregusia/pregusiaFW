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


class DateUnit extends Enum {

	const DAY = 1;
	const WEEK = 2;
	const MONTH = 3;
	const YEAR = 4;
	
	//************************************************************************************
	public function __construct() {
		parent::__construct(array(
			self::DAY => 'd',
			self::WEEK => 'w',
			self::MONTH => 'm',
			self::YEAR => 'y',
		));
	}
	
	//************************************************************************************
	public static function FromStr($str) {
		$str = strtolower($str);
		switch($str) {
			case 'm': return self::MONTH;
			case 'y': return self::YEAR;
			case 'd': return self::DAY;
			case 'w': return self::WEEK;
			default: throw new InvalidArgumentException('Unknown DateUnit value - ' . $str);			
		}
	}
	
}

?>