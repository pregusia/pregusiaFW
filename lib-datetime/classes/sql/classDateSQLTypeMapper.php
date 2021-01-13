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
 * 
 * @author pregusia
 * @NeedLibrary lib-storage-sql
 *
 */
class DateSQLTypeMapper implements ISQLTypeMapper {
	
	//************************************************************************************
	public function getPriority() { return 10; }
	
	//************************************************************************************
	/**
	 * Stwierdza czy ten adapter umie zmapowac dany typ SQL na typ PHP
	 * @param int $sqlType
	 * @return bool
	 */
	public function canFromSQL($sqlType) {
		if ($sqlType == SQLTypeEnum::DATE) return true;
		if ($sqlType == SQLTypeEnum::TIMESTAMP) return true;
		if ($sqlType == SQLTypeEnum::DATETIME) return true;
		if ($sqlType == SQLTypeEnum::TIME) return true;
		return false;
	}
	
	//************************************************************************************
	/**
	 * Mapuje dany typ SQL na typ PHP
	 * @param int $sqlType
	 * @param string $rawValue
	 * @return mixed
	 */
	public function fromSQL($sqlType, $rawValue) {
		if ($sqlType == SQLTypeEnum::DATE) return Date::FromString($rawValue);
		if ($sqlType == SQLTypeEnum::TIME) return Time::FromString($rawValue);
		if ($sqlType == SQLTypeEnum::DATETIME) return DateAndTime::FromString($rawValue);
		if ($sqlType == SQLTypeEnum::TIMESTAMP) return DateAndTime::FromTimestamp($rawValue);
		throw new InvalidArgumentException('Invalid SQLType given');
	}

	//************************************************************************************
	/**
	 * Stwierdza czy podana wartosc PHP moze zostac zmapowana na typ SQL
	 * @param mixed $value
	 * @return bool
	 */
	public function canToSQL($value) {
		if ($value instanceof Date) return true;
		if ($value instanceof Time) return true;
		if ($value instanceof DateAndTime) return true;
		return false;
	}
	
	//************************************************************************************
	/**
	 * Mapuje podana wartosc PHP na string uzywany do zapytan SQL
	 * @param mixed $value
	 * @param ISQLValueEscaper $oEscaper
	 * @return string
	 */
	public function toSQL($value, $oEscaper) {
		if (($value instanceof Date) || ($value instanceof Time) || ($value instanceof DateAndTime)) {
			return sprintf('"%s"', $value->toString());
		} 
		throw new InvalidArgumentException('Given value is invalid');
	}
	
	
}

?>