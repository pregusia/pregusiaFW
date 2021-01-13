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
 * Zapewnia mapowania typow PHP do SQL i odwrotnie
 * Nie jest to rozbite na dwa osobne interfejsy (tj. do SQL i od SQL) w celu zmniejszenia rozdziabania kodu 
 * @author pregusia
 *
 */
interface ISQLTypeMapper {
	
	/**
	 * @return int
	 */
	public function getPriority();
	
	/**
	 * Stwierdza czy ten adapter umie zmapowac dany typ SQL na typ PHP
	 * @param int $sqlType
	 * @return bool
	 */
	public function canFromSQL($sqlType);
	
	/**
	 * Mapuje dany typ SQL na typ PHP
	 * @param int $sqlType
	 * @param string $rawValue
	 * @return mixed
	 */
	public function fromSQL($sqlType, $rawValue);

	/**
	 * Stwierdza czy podana wartosc PHP moze zostac zmapowana na typ SQL
	 * @param mixed $value
	 * @return bool
	 */
	public function canToSQL($value);
	
	/**
	 * Mapuje podana wartosc PHP na string uzywany do zapytan SQL
	 * @param mixed $value
	 * @param ISQLValueEscaper $oEscaper
	 * @return string
	 */
	public function toSQL($value, $oEscaper);
	
}

?>