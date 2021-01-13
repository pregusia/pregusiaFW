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


abstract class ORMField {
	
	/**
	 * @var ORMTableFieldDefinition
	 */
	private $oDefinition = null;
	
	/**
	 * @var ORMTableRecord
	 */
	private $oRecord = null;
	
	/**
	 * @var IEnumerable
	 */
	private $oValuesSource = false;
	
	protected $changed = false;
	
	//************************************************************************************
	/**
	 * @return ORMTableFieldDefinition
	 */
	public function getDefinition() { return $this->oDefinition; }
	public function setDefinition($v) { $this->oDefinition = $v; return $this; }
	
	//************************************************************************************
	/**
	 * @return ORM
	 */
	public function getORM() {
		if ($this->getRecord()) {
			return $this->getRecord()->getORM();
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 */
	public function getRecord() { return $this->oRecord; }
	public function setRecord($v) { $this->oRecord = $v; }
	
	//************************************************************************************
	public function isChanged() { return $this->changed; }
	public function setChanged($v) { $this->changed = $v; return $this; }
	
	//************************************************************************************
	public abstract function set($v);
	public abstract function get();
	public abstract function isNull();
	
	//************************************************************************************
	/**
	 * @param ISQLValueEscaper $oEscaper
	 */
	public abstract function toSQL($oEscaper);
	
	//************************************************************************************
	public function tplRender($oContext) {
		if ($this->isNull()) return '[null]';
		return htmlspecialchars($this->get());
	}
	
	//************************************************************************************
	public function load($val) {
		$this->set($val);
		$this->setChanged(false);
	}
	
	//************************************************************************************
	public function onApplyOption($name, $value) {
		
	}
	
	//************************************************************************************
	/**
	 * @return IEnumerable
	 */
	public function getValuesSource() {
		if ($this->oValuesSource === false) {
			$oClass = $this->getDefinition()->getValuesSource();
			if ($oClass) {
				if ($this->getDefinition()->isValuesSourceContextual()) {
					$obj = $oClass->getInstance();
					if ($obj instanceof IORMFieldValuesSource) {
						$obj->onInit($this, $this->getRecord(), $this->getRecord() ? $this->getRecord()->getTable() : null);
					}
					$this->oValuesSource = $obj;
				} else {
					$this->oValuesSource = $this->getDefinition()->getValuesSourceObject();
				}
			} else {
				$this->oValuesSource = null;
			}
		}
		
		return $this->oValuesSource;
	}
	
}

?>