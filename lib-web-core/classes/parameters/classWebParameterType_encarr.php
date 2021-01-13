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


class WebParameterType_encarr implements IWebParameterType {
	
	const KEY = 'Erah8aeh7Aigae8ooShaido1eeghium8lee7ahc7gaYeebeib5aat7xe7leer7Oh';
	
	//************************************************************************************
	public function getName() { return 'encarr'; }
	
	//************************************************************************************
	public function unserialize($v) {
		return UtilsString::urlSafeDecode($v, self::KEY);
	}
	
	//************************************************************************************
	public function serialize($v) {
		if (is_array($v)) {
			return UtilsString::urlSafeEncode($v, self::KEY);
		} else {
			return '';
		}
	}
	
}

?>