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
 * Tlumaczenie jakiejs frazy
 * Przetlumaczona fraza moze miec zalesnoc od liczby
 * 
 * @author pregusia
 *
 */
class I18NTranslation {
	
	private $translated = '';
	private $plurals = array();
	
	//************************************************************************************
	public function getTranslated() { return $this->translated; }
	
	//************************************************************************************
	public function getPlural($cls) {
		return $this->plurals[$cls];
	}
	
	//************************************************************************************
	/**
	 * @param string $orig
	 * @param string $translated
	 * @return I18NTranslation
	 */
	public static function CreateSimple($translated) {
		$obj = new I18NTranslation();
		$obj->translated = $translated;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param string $orig
	 * @param string[] $plurals
	 * @return I18NTranslation
	 */
	public static function CreatePlural($plurals) {
		$obj = new I18NTranslation();
		$obj->plurals = $plurals;
		return $obj;
	}
	
}

?>