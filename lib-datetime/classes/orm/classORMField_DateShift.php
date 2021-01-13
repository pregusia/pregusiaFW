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


/**
 * 
 * @author pregusia
 * @NeedLibrary lib-orm
 *
 */
class ORMField_DateShift extends ORMField_String {
	
	//************************************************************************************
	public function set($v) {
		if ($v instanceof DateShift) {
			$v = $v->toString();
		}
		return parent::set($v);
	}
	
	//************************************************************************************
	/**
	 * @return DateShift
	 */
	public function getDateShift() {
		try {
			return DateShift::CreateFromString($this->get());
		} catch(Exception $e) {
		}
		return null;
	}
	
}

?>