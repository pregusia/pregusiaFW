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


class ORMField_Flags extends ORMField_Integer {
	
	//************************************************************************************
	/**
	 * @return FlagsEnum
	 */
	public function getFlags() {
		$oSource = $this->getValuesSource();
		if (!($oSource instanceof FlagsEnum)) throw new IllegalStateException('ValuesSource is not FlagsEnum');
		return $oSource;
	}
	
	//************************************************************************************
	public function isValid($f) {
		return $this->getFlags()->contains($f);
	}
	
	//************************************************************************************
	public function add($v) {
		if (!$this->isValid($v)) throw new InvalidArgumentException('Invalid flag');
		$value = intval($this->get());
		$this->set($value | $v);
	}
	
	//************************************************************************************
	public function remove($v) {
		if (!$this->isValid($v)) throw new InvalidArgumentException('Invalid flag');
		$value = intval($this->get());
		$this->set($value & ~$v);
	}
	
	//************************************************************************************
	public function toggle($flag, $state) {
		if ($state) {
			$this->add($flag);
		} else {
			$this->remove($flag);
		}
	}
	
	//************************************************************************************
	public function has($v) {
		if ($this->isNull()) return false;
		return ($this->get() & $v) == $v;
	}
	
	//************************************************************************************
	public function asString() {
		if ($this->isNull()) return '[null]';
		return $this->getFlags()->toString($this->get());
	}
	
}

?>