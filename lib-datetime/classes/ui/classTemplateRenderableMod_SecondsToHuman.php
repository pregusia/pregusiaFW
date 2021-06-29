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
 * @NeedLibrary lib-templating
 *
 */
class TemplateRenderableMod_SecondsToHuman implements ITemplateRenderableMod {
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() {
		return 10;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		
	}	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() {
		return 'secondsToHuman';
	}
	
	//************************************************************************************
	/**
	 * @param TemplateRenderableProxyContext $oContext
	 * @param mixed $value
	 * @param array $params
	 */
	public function apply($oContext, $value, $params) {
		$value = intval($value);
		
		$days = floor($value / 86400);
		$value = $value % 86400;
		
		$hours = floor($value / 3600);
		$value = $value % 3600;
		
		$minutes = floor($value / 60);
		$seconds = $value % 60;
		
		$arr = array();
		if ($days > 0) $arr[] = sprintf('%dd', $days);
		if ($hours > 0) $arr[] = sprintf('%dh', $hours);
		if ($minutes > 0) $arr[] = sprintf('%dmin', $minutes);
		if ($seconds > 0) $arr[] = sprintf('%dsec', $seconds);
		
		return implode(' ',$arr);
	}
	
}

?>