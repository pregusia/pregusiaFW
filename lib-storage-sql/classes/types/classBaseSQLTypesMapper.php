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


class BaseSQLTypesMapper implements ISQLTypeMapper {

	//************************************************************************************
	public function getPriority() {
		return 1;
	}
	
	//************************************************************************************
	/**
	 * Stwierdza czy ten adapter umie zmapowac dany typ SQL na typ PHP
	 * @param int $sqlType
	 * @return bool
	 */
	public function canFromSQL($sqlType) {
		if ($sqlType == SQLTypeEnum::INT) return true;
		if ($sqlType == SQLTypeEnum::DATE) return true;
		if ($sqlType == SQLTypeEnum::DATETIME) return true;
		if ($sqlType == SQLTypeEnum::DECIMAL) return true;
		if ($sqlType == SQLTypeEnum::DOUBLE) return true;
		if ($sqlType == SQLTypeEnum::FLOAT) return true;
		if ($sqlType == SQLTypeEnum::STRING) return true;
		if ($sqlType == SQLTypeEnum::TIME) return true;
		if ($sqlType == SQLTypeEnum::TIMESTAMP) return true;
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
		switch($sqlType) {
			case SQLTypeEnum::INT:
				return intval($rawValue);
				
			case SQLTypeEnum::FLOAT: 
			case SQLTypeEnum::DOUBLE:
			case SQLTypeEnum::DECIMAL:
				return floatval($rawValue);
				
			case SQLTypeEnum::DATE: 
			case SQLTypeEnum::DATETIME: 
			case SQLTypeEnum::TIME: 
				return strval($rawValue);
				
			case SQLTypeEnum::STRING:
			case SQLTypeEnum::BINARY:
				return strval($rawValue);
				
			case SQLTypeEnum::TIMESTAMP: return intval($rawValue);
			
			default:
				throw new InvalidArgumentException('Not handled SQL type supplied');
		}
	}

	//************************************************************************************
	/**
	 * Stwierdza czy podana wartosc PHP moze zostac zmapowana na typ SQL
	 * @param mixed $value
	 * @return bool
	 */
	public function canToSQL($value) {
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
		throw new UnsupportedOperationException();
	}
	
}

?>