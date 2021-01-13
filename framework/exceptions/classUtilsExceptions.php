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


class UtilsExceptions {
	
	//************************************************************************************
	public static function setMessage($e, $msg) {
		if (!($e instanceof Exception)) throw new InvalidArgumentException();
		$oClass = new ReflectionClass($e);
		$oProperty = $oClass->getProperty('message');
		$oProperty->setAccessible(true);
		$oProperty->setValue($e, $msg);
	}

	//************************************************************************************
	public static function setCode($e, $code) {
		if (!($e instanceof Exception)) throw new InvalidArgumentException();
		$oClass = new ReflectionClass($e);
		$oProperty = $oClass->getProperty('code');
		$oProperty->setAccessible(true);
		$oProperty->setValue($e, $code);
	}	
	
	//************************************************************************************
	public static function getInfoFields($e, &$fields) {
		$oClass = new ReflectionClass($e);
		foreach($oClass->getMethods() as $oMethod) {
			false && $oMethod = new ReflectionMethod();
			
			if ($oMethod->getName() == 'infoFieldsArray') {
				$val = $oMethod->invoke($e);
				if (is_array($val)) {
					foreach($val as $k => $v) {
						$fields[$k] = strval($v);
					}
				}
			}
			elseif (substr($oMethod->getName(),0,4) == 'info') {
				$val = $oMethod->invoke($e);
				if ($val) {
					$fields[$oMethod->getName()] = $val;
				}
			}
		}		
	}
	
	//************************************************************************************
	public static function getInfoFieldsRet($e) {
		$arr = array();
		self::getInfoFields($e, $arr);
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param Exception $e
	 */
	public static function toArray($e, $includeTrace=true) {
		$fields = array();
		$fields['message'] = $e->getMessage();
		$fields['className'] = get_class($e);
		$fields['code'] = $e->getCode();
		
		if ($includeTrace) {
			$fields['trace'] = $e->getTraceAsString();
		}
		
		self::getInfoFields($e, $fields);
		return $fields;
	}
	
	//************************************************************************************
	/**
	 * @param Exception $e
	 */
	public static function toString($e) {
		return sprintf('%s: %s', get_class($e), $e->getMessage());
	}
	
}

?>