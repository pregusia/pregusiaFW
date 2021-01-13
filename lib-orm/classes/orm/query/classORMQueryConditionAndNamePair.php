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


class ORMQueryConditionAndNamePair {
	
	private $fieldName = '';
	
	/**
	 * @var IORMQueryCondition
	 */
	private $oCondition = null;
	
	//************************************************************************************
	public function __construct($fieldName, $oCondition) {
		if (!($oCondition instanceof IORMQueryCondition)) throw new InvalidArgumentException('oCondition is not IORMQueryCondition');
		
		$fieldName = trim($fieldName);
		if (!$fieldName) throw new InvalidArgumentException('fieldName is empty');
		
		$this->oCondition = $oCondition;
		$this->fieldName = $fieldName;
	}
	
	//************************************************************************************
	/**
	 * @return IORMQueryCondition
	 */
	public function getCondition() { return $this->oCondition; }
	
	//************************************************************************************
	public function getFieldName() { return $this->fieldName; }
	
	//************************************************************************************
	/**
	 * @param ORM $oORM
	 * @return string
	 */
	public function toSQL($oORM) {
		if (!($oORM instanceof ORM)) throw new InvalidArgumentException('oORM is not ORM');
		
		return $this->getCondition()->toSQL($this->getFieldName(), $oORM);
	}
	
}

?>