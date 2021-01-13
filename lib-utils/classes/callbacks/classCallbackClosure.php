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


class CallbackClosure implements ICallback {
	
	const CALL_SUCCESS = 1;
	const CALL_ERROR = 2;
	
	
	private $func = null;
	
	
	//************************************************************************************
	public function __construct($func) {
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		$this->func = $func;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $returnValue
	 */
	public function onSuccess($returnValue) {
		$func = $this->func;
		$func(self::CALL_SUCCESS, $returnValue, null);
	}
	
	//************************************************************************************
	/**
	 * @param Exception $oException
	 */
	public function onError($oException) {
		$func = $this->func;
		$func(self::CALL_ERROR, null, $oException);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return UtilsJSON::serializeClosure($this->func);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return CallbackClosure
	 */
	public static function jsonUnserialize($arr) {
		$func = UtilsJson::unserializeClosure($arr);
		if ($func instanceof Closure) {
			return new CallbackClosure($func);
		} else {
			return null;
		}
	}
	
}

?>