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


class SQLResultsColumn {
	
	/**
	 * @var SQLResultsRow
	 */
	private $oRow = null;
	
	/**
	 * @var SQLNamePair
	 */
	private $oColumnName = null;
	
	/**
	 * @var SQLNamePair
	 */
	private $oTableName = null;
	
	private $index = 0;
	private $type = 0;
	private $valueRaw = '';

	//************************************************************************************
	/**
	 * @return SQLResultsRow
	 */
	public function getRow() { return $this->oRow; }
	
	//************************************************************************************
	/**
	 * @return SQLNamePair
	 */
	public function getColumnName() { return $this->oColumnName; }
	
	//************************************************************************************
	/**
	 * @return SQLNamePair
	 */
	public function getTableName() { return $this->oTableName; }
	
	//************************************************************************************
	public function getIndex() { return $this->index; }
	public function getType() { return $this->type; }
	public function getValueRaw() { return $this->valueRaw; }
	
	//************************************************************************************
	public function getFullName() {
		if ($this->getTableName() && $this->getColumnName()) {
			return $this->getTableName()->getOrginal() . '.' . $this->getColumnName()->getOrginal();
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	public function __construct($oRow, $index, $oTableName, $oColumnName, $type, $valueRaw) {
		if (!($oRow instanceof SQLResultsRow)) throw new InvalidArgumentException('oRow is not SQLResultsRow');
		if ($index < 0) throw new InvalidArgumentException('index < 0');
		if (!SQLTypeEnum::isValid($type)) throw new InvalidEnumValueException('SQLTypeEnum', $type);
		if (!($oColumnName instanceof SQLNamePair)) throw new InvalidArgumentException('oColumnName is not SQLNamePair');  
		if ($oTableName && !($oTableName instanceof SQLNamePair)) throw new InvalidArgumentException('oTableName is not SQLNamePair');
		
		$this->oRow = $oRow;
		$this->oColumnName = $oColumnName;
		$this->oTableName = $oTableName;
		$this->index = $index;
		$this->type = $type;
		$this->valueRaw = $valueRaw;
	}
	
	//************************************************************************************
	public function getValueMapped() {
		$oComponent = ApplicationContext::getCurrent()->getComponent('storage.sql');
		false && $oComponent = new SQLStorageApplicationComponent();

		return $oComponent->getTypesMapper()->fromSQL($this->type, $this->valueRaw);
	}
	
}

?>