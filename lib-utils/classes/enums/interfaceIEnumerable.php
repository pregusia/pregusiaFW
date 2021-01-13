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
 * Zapewnia mozliwosc enumeracji obiektow
 * Zarowno w sposob statyczny oraz suggestowy
 * @author pregusia
 *
 */
interface IEnumerable {
	
	const USAGE_SIMPLE = 1;
	const USAGE_SUGGEST = 2;
	
	public function enumerableUsageType();
	
	/**
	 * Zwraca wyliczenie wszystkich elementow
	 * @return Enum
	 */
	public function enumerableGetAllEnum();
	
	/**
	 * @param mixed $param
	 * @return ComplexString
	 */
	public function enumerableToString($param);
	
	/**
	 * Zwraca sugestie
	 * @param string $text
	 * @return Enum
	 */
	public function enumerableSuggest($text);
	
}

?>