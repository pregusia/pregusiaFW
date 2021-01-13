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


class ORMTableAndKeyPair {
	
	private $oTable = null;
	private $oKey = null;

	//************************************************************************************
	/**
	 * @return ORMTable
	 */
	public function getTable() { return $this->oTable; }
	
	//************************************************************************************
	/**
	 * @return ORMKey
	 */
	public function getKey() { return $this->oKey; }
	
	//************************************************************************************
	public function __construct($oTable, $oKey) {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('Table is not ORMTable');
		if (!($oKey instanceof ORMKey)) throw new InvalidArgumentException('Key is not ORMKey');
		if ($oKey->getCount() == 0) throw new InvalidArgumentException('Empty key');
		$this->oTable = $oTable;
		$this->oKey = $oKey;
	}
	
	//************************************************************************************
	public function isKeyUnique() {
		return $this->getTable()->isKeyUnique($this->getKey());
	}
	
	//************************************************************************************
	public function isKeyNull() {
		return $this->getTable()->isKeyNull($this->getKey());
	}
	
}

?>