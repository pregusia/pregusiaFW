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


class SQLResultsRow implements IteratorAggregate {
	
	/**
	 * @var ISQLResultsSet
	 */
	private $oResultsSet = null;
	
	/**
	 * @var SQLResultsColumn[]
	 */
	private $columns = array();
	
	//************************************************************************************
	/**
	 * @return ISQLResultsSet
	 */
	public function getResultsSet() { return $this->oResultsSet; }
	
	//************************************************************************************
	/**
	 * @return SQLResultsColumn[]
	 */
	public function getColumns() { return $this->columns; }
	
	//************************************************************************************
	public function getColumnsCount() { return count($this->columns); }
	
	//************************************************************************************
	/**
	 * @param ISQLResultsSet $oResultsSet
	 * @param SQLResultsColumn[] $Columns
	 */
	public function __construct($oResultsSet) {
		if (!($oResultsSet instanceof ISQLResultsSet)) throw new InvalidArgumentException('oResultsSet is not ISQLResultsSet');
		$this->oResultsSet = $oResultsSet;		
	}
	
	//************************************************************************************
	/**
	 * @param int $index
	 * @param SQLNamePair $oTableName
	 * @param SQLNamePair $oColumnName
	 * @param int $type
	 * @param mixed $valueRaw
	 * @return SQLResultsColumn
	 */
	public function addColumn($index, $oTableName, $oColumnName, $type, $valueRaw) {
		$oColumn = new SQLResultsColumn($this, $index, $oTableName, $oColumnName, $type, $valueRaw);
		
		if ($this->getColumnByIndex($index)) throw new InvalidArgumentException(sprintf('Column with index %d already exists', $index));

		if ($oColumnName->getName()) $this->columns[$oColumnName->getName()] = $oColumn;
		if ($oColumnName->getOrginal()) $this->columns[$oColumnName->getOrginal()] = $oColumn;
		if ($oColumn->getFullName()) $this->columns[$oColumn->getFullName()] = $oColumn;
		
		return $oColumn;
	}
	
	//************************************************************************************
	/**
	 * @param int $idx
	 * @return SQLResultsColumn
	 */
	public function getColumnByIndex($idx) {
		foreach($this->columns as $oColumn) {
			if ($oColumn->getIndex() == $idx) {
				return $oColumn;
			}
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return SQLResultsColumn
	 */
	public function getColumnByName($name) {
		return $this->columns[$name];
	}
	
	//************************************************************************************
	/**
	 * @param int $ref
	 * @return SQLResultsColumn
	 */
	public function getColumn($ref) {
		if (ctype_digit($ref)) {
			return $this->getColumnByIndex(intval($ref));
		} else {
			return $this->getColumnByName($ref);
		}
	}
	
	//************************************************************************************
	/**
	 * @return SQLResultsColumn
	 */
	public function getFirstColumn() {
		return UtilsArray::getFirst($this->columns);
	}
	
	//************************************************************************************
	public function getIterator() {
		return new ArrayIterator($this->columns);
	}
	
}

?>