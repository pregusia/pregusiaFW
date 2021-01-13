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

class UtilsHTTP {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * @param IHTTPClientRequest $oClient
	 * @param array $jsonRequest
	 * @return array
	 */
	public static function postJSON($oClient, $jsonRequest) {
		if (!($oClient instanceof IHTTPClientRequest)) throw new InvalidArgumentException('oClient is not IHTTPClientRequest');
		if (!is_array($jsonRequest)) throw new InvalidArgumentException('jsonRequest is not array');
		
		$oClient->setMethod(HTTPMethod::POST);
		$oClient->setContentType('application/json');
		$oClient->setRequestContent(json_encode($jsonRequest));
		$oResponse = $oClient->run();
		
		if (strpos($oResponse->getContentType(), 'application/json') === false) {
			throw new IOException(sprintf('Response Content-Type is not application/json (is %s)', $oResponse->getContentType()));
		} 
		
		$content = trim($oResponse->getContentString());
		$jsonResponse = @json_decode($content, true);
		
		if ($jsonResponse === null && $content != 'null') {
			throw new IOException('Got not proper JSON data in response');
		}
		
		return $jsonResponse;
	}
	
	//************************************************************************************
	/**
	 * @param IHTTPClientRequest $oClient
	 * @param array $jsonRequest
	 * @param ICallback $oCallback
	 */
	public static function postJSONAsync($oClient, $jsonRequest, $oCallback) {
		if (!($oClient instanceof IHTTPClientRequest)) throw new InvalidArgumentException('oClient is not IHTTPClientRequest');
		if (!is_array($jsonRequest)) throw new InvalidArgumentException('jsonRequest is not array');
		
		$oClient->setMethod(HTTPMethod::POST);
		$oClient->setContentType('application/json');
		$oClient->setRequestContent(json_encode($jsonRequest));
		
		if ($oCallback) {
			if (!($oCallback instanceof ICallback)) throw new InvalidArgumentException('oCallback is not ICallback');
			
			$oClient->runAsync(new CallbackClosure(function($mode, $returnValue, $oException) use ($oCallback) {
				if ($mode == CallbackClosure::CALL_ERROR) $oCallback->onError($oException);
				if ($mode == CallbackClosure::CALL_SUCCESS) {
					if ($returnValue instanceof IHTTPClientResponse) {
						if (strpos($returnValue->getContentType(), 'applicaton/json') === false) {
							$oCallback->onError(new IOException(sprintf('Response Content-Type is not application/json (is %s)', $returnValue->getContentType())));
						} else {
							$oCallback->onSuccess($returnValue->getContentJSON());
						}
					} else {
						$oCallback->onError(new IllegalStateException('Invalid type of arg'));
					}
				}
			}));
		} else {
			$oClient->runAsync(null);
		}
	}
	
}

?>