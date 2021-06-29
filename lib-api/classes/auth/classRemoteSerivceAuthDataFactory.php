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


class RemoteServiceAuthDataFactory {
	
	private function __construct() { }
	
	
	
	//************************************************************************************
	/**
	 * @param string $headerValue
	 * @return IRemoteServiceAuthData
	 */
	public static function UnserializeHeader($value) {
		$value = trim($value);
		if (!$value) return null;
		
		$arr = explode(' ',$value);
		if (count($arr) == 2 && $arr[0] == "Bearer") {

			$tokenVal = trim($arr[1]);
			if (!$tokenVal) return null;
			
			// ok, to moze byc po prostu key, albo JWT Token
			// trzeba sprawdzic
			
			$tokenArr = explode('.',$tokenVal);
			if (count($tokenArr) == 3) {
				// to pewno JWT, wiec parsujemy
				try {
					return new RemoteServiceAuthData_JWTToken($tokenVal);
				} catch(Exception $ex) {
					Logger::warn('RemoteServiceAuthDataFactory::UnserializeHeader', $ex, array(
						'headerValue' => $value,
					));
				}
				
				return null;
			} else {
				// ok, pewno RAW
				return new RemoteServiceAuthData_String($tokenVal);
			}
		}
		elseif (count($arr) == 2 && $arr[0] == "Basic") {
			
			$tokenVal = trim($arr[1]);
			if (!$tokenVal) return null;
			
			$tokenValDecoded = @base64_decode($tokenVal);
			if (!$tokenValDecoded) return null;
			
			list($a, $b) = explode(':',$tokenValDecoded,2);
			
			$a = trim($a);
			$b = trim($b);
			
			if (!$a) return null;
			if (!$b) return null;
			
			return new RemoteServiceAuthData_Basic($a, $b);
		} 
		
		
		return null;		
	}
	
	//************************************************************************************
	/**
	 * @param string $typeName
	 * @return CodeBaseDeclaredClass
	 */
	public static function getClassByType($typeName) {
		if (UtilsString::startsWith($typeName, 'RemoteServiceAuthData_')) {
			return CodeBase::getClass($typeName, false);
		} else {
			foreach(CodeBase::getClassesImplementing('IRemoteServiceAuthData') as $oClass) {
				if (UtilsString::startsWith($oClass->getName(), 'RemoteServiceAuthData_')) {
					
					$nn = substr($oClass->getName(), 22);
					if (strtolower($nn) == strtolower($typeName)) {
						return $oClass;
					}
				}
			}
		}
		
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @return IRemoteServiceAuthData
	 */
	public static function UnserializeJSON($value) {
		if (!$value) return null;
		
		
		if (is_string($value)) {
			$value = trim($value);
			if ($value) {
				return new RemoteServiceAuthData_String($value);
			} else {
				return null;
			}		
		}
		elseif (is_array($value)) {
			if ($value['type']) {
				$oClass = self::getClassByType($value['type']);
				if ($oClass) {
					return $oClass->callStaticMethod('jsonUnserialize', array( $value ));
				} else {
					return null;
				}
			} else {
				return null;
			}
		}
		else {
			return null;
		}
	}
	
	
}

?>