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


class WebActionWrapper_JSON implements IWebActionWrapper {
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param string[] $params
	 */
	public function onBegin($oActionDef, &$params) {
		
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param mixed $returnValue
	 * @return WebResponseBase
	 */
	public function onEnd($oActionDef, $returnValue) {
		if ($returnValue instanceof WebResponseJson) {
			return $returnValue;
		}
		
		if (is_array($returnValue)) {
			if (!isset($returnValue['stauts'])) {
				$returnValue['status'] = 'ok';
			}
			return new WebResponseJson($returnValue);	
		}
		
		if (is_string($returnValue) || is_bool($returnValue) || is_int($returnValue) || is_float($returnValue) || is_null($returnValue)) {
			$res = array();
			$res['status'] = 'ok';
			$res['returnValue'] = $returnValue;
			return new WebResponseJson($res);
		}
		
		if (is_object($returnValue) && ($returnValue instanceof JsonSerializable)) {
			$res = array();
			$res['status'] = 'ok';
			$res['returnValue'] = $returnValue->jsonSerialize();
			return new WebResponseJson($res);			
		}
		
		throw new IllegalStateException(sprintf('Action %s returned invalid value for wrapper WebActionWrapper_JSON', $oActionDef->getFirstName()));
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param Exception $oException
	 * @return WebResponseBase
	 */
	public function onException($oActionDef, $oException) {
		$oResponse = new WebResponseJson(array(
			'status' => 'error',
			'errorText' => UtilsExceptions::toString($oException),
			'exception' => UtilsExceptions::toArray($oException, false)
		));
		
		if ($oException instanceof ObjectNotFoundException) {
			$oResponse->setHttpCode(404);
		}
		if ($oException instanceof SecurityException) {
			$oResponse->setHttpCode(403);
		}
		
		return $oResponse;
	}
	
}

?>