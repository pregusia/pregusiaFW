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


class SQLTypesMapper {
	
	private $mappers = array();
	
	//************************************************************************************
	/**
	 * @return ISQLTypeMapper[]
	 */
	public function getMappers() { return $this->mappers; }
	
	//************************************************************************************
	public function __construct() {
		$this->mappers = CodeBase::getInterface('ISQLTypeMapper')->getAllInstances();
		
		usort($this->mappers, function($a,$b){
			return $a->getPriority() - $b->getPriority();
		});
	}
	
	//************************************************************************************
	/**
	 * Mapuje wartosc na ciag znakow uzywany do zapytan SQL 
	 * @param mixed $value
	 * @param ISQLValueEscaper $oEscaper
	 */
	public function toSQL($value, $oEscaper) {
		if (!($oEscaper instanceof ISQLValueEscaper)) throw new InvalidArgumentException('oEscaper is not ISQLValueEscaper');
		
		$typeName = strtolower(gettype($value));
		if ($typeName == 'object') {
			$className = get_class($value);
		} else {
			$className = '';
		}
		
		if ($typeName == 'boolean') return $value ? '1' : '0';
		if ($typeName == 'integer') return intval($value);
		if ($typeName == 'double') return sprintf('%.4f', $value);
		if ($typeName == 'null') return 'NULL';
		if ($typeName == 'string') return sprintf('"%s"', $oEscaper->escapeString($value));
		
		if ($typeName == 'object') {
			foreach($this->getMappers() as $oMapper) {
				if ($oMapper->canToSQL($value)) {
					return $oMapper->toSQL($value, $oEscaper);
				}
			}
		}
		
		throw new SQLException(sprintf('Could not map value of type %s (class %s) to SQL', $typeName, $className));
	}
	
	//************************************************************************************
	/**
	 * Mapuje wartosc pozyskana z SQLa na typ PHP
	 * @param int $sqlType
	 * @param string $rawValue
	 * @return mixed
	 */
	public function fromSQL($sqlType, $rawValue) {
		if (gettype($rawValue) == 'NULL') return null;
		
		foreach($this->getMappers() as $oMapper) {
			if ($oMapper->canFromSQL($sqlType)) {
				return $oMapper->fromSQL($sqlType, $rawValue);
			}
		}
		
		return strval($rawValue);
	}
	
}

?>