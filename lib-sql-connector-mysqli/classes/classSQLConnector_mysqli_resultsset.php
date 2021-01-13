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


class SQLConnector_mysqli_resultsset implements ISQLResultsSet {

	/**
	 * @var ISQLConnector
	 */
	private $oConnector = null;
	
	/**
	 * @var mysqli_result
	 */
	private $result = null;
	
	//************************************************************************************
	/**
	 * @return ISQLConnector
	 */
	public function getConnector() {
		return $this->oConnector;
	}
	
	//************************************************************************************
	public function __construct($oConnector, $result) {
		if (!($oConnector instanceof ISQLConnector)) throw new InvalidArgumentException('oConnector is not ISQLConnector');
		$this->oConnector = $oConnector;
		
		if ($result instanceof mysqli_result) {
			$this->result = $result;
		} else {
			$this->result = null;
		}
	}
	
	//************************************************************************************
	public function close() {
		if ($this->result) {
			$this->result->close();
			$this->result = null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return SQLResultsRow
	 */
	public function next() {
		if ($this->result) {
			$row = $this->result->fetch_array(MYSQLI_NUM);
			if (!$row) return null;
			
			$oRow = new SQLResultsRow($this);
			
			foreach($row as $idx => $fieldValueRaw) {
				$fieldInfo = $this->result->fetch_field_direct($idx);
				$oColumnName = SQLNamePair::Create($fieldInfo->name, $fieldInfo->orgname);
				$oTableName = SQLNamePair::Create($fieldInfo->table, $fieldInfo->orgtable);
				
				$oRow->addColumn(
					$idx,
					$oTableName,
					$oColumnName,
					self::normalizeSQLType($fieldInfo->type),
					$fieldValueRaw
				);
			}
			
			return $oRow;
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	public static function normalizeSQLType($type) {
		switch($type) {
			case MYSQLI_TYPE_TINY:
			case MYSQLI_TYPE_SHORT:
			case MYSQLI_TYPE_LONG:
			case MYSQLI_TYPE_INT24:
			case MYSQLI_TYPE_LONGLONG:
			case MYSQLI_TYPE_YEAR:
				return SQLTypeEnum::INT;
				
			case MYSQLI_TYPE_TIMESTAMP:
				return SQLTypeEnum::TIMESTAMP;
				
			case MYSQLI_TYPE_DECIMAL:
			case MYSQLI_TYPE_NEWDECIMAL:
				return SQLTypeEnum::DECIMAL;
				
			case MYSQLI_TYPE_FLOAT:
				return SQLTypeEnum::FLOAT;
				
			case MYSQLI_TYPE_DOUBLE:
				return SQLTypeEnum::DOUBLE;
				
			case MYSQLI_TYPE_STRING:
			case MYSQLI_TYPE_VAR_STRING:
			case MYSQLI_TYPE_CHAR:
				return SQLTypeEnum::STRING;
				
			case MYSQLI_TYPE_BLOB:
			case MYSQLI_TYPE_LONG_BLOB:
				return SQLTypeEnum::BINARY;
				
			case MYSQLI_TYPE_DATE:
			case MYSQLI_TYPE_NEWDATE:
				return SQLTypeEnum::DATE;
				
			case MYSQLI_TYPE_DATETIME:
				return SQLTypeEnum::DATETIME;

			case MYSQLI_TYPE_TIME:
				return SQLTypeEnum::TIME;
				
			default:
				throw new InvalidArgumentException(sprintf('Unsupported mysqli type %d', $type));
		}
	}
	
}

?>