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


class ORMTableRecordAndRelationDefinitionPair {
	
	private $oRecord = null;
	private $oRelationDef = null;
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 */
	public function getRecord() { return $this->oRecord; }
	
	//************************************************************************************
	/**
	 * @return ORMRelationDefinition
	 */
	public function getRelationDefinition() { return $this->oRelationDef; }
	
	//************************************************************************************
	/**
	 * @param ORMTableRecord $oRecord
	 * @param ORMRelationDefinition $oRelationDef
	 */
	public function __construct($oRecord, $oRelationDef) {
		if (!($oRecord instanceof ORMTableRecord)) throw new InvalidArgumentException('oRecord is not ORMTableRecord');
		if (!($oRelationDef instanceof ORMRelationDefinition)) throw new InvalidArgumentException('oRelationDef is not ORMRelationDefinition');
		$this->oRecord = $oRecord;
		$this->oRelationDef = $oRelationDef;
	}
	
}

?>