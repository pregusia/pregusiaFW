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


class APIProcessingErrorException extends Exception {
	
	const CODE_SERVER_REQUEST_INVALID_HTTP_METHOD = -32600;
	const CODE_SERVER_REQUEST_INVALID_CONTENT_TYPE = -32600;
	const CODE_SERVER_REQUEST_JSON_PARSE_ERROR = -32700;
	
	const CODE_SERVER_SUPPLIER_NOT_FOUND = -32601;
	const CODE_SERVER_METHOD_NOT_FOUND = -32601;
	const CODE_SERVER_REQUEST_INVALID_AUTH = -32602;
	
	const CODE_CLIENT_RESPONSE_EMPTY = 201;
	const CODE_CLIENT_RESPONSE_JSON_PARSE_ERROR = 202;
	const CODE_CLIENT_RESPONSE_JSONRPC_VERSION = 210;
	const CODE_CLIENT_RESPONSE_JSONRPC_ID_MISMATCH = 211;
	const CODE_CLIENT_RESPONSE_JSONRPC_RESULT_MISSING = 212;
	
	
	
	private $httpCode = 500;
	
	//************************************************************************************
	public function apiHttpCode() { return $this->httpCode; }
	
	//************************************************************************************
	public function __construct($message='', $code=0, $httpCode=500) {
		parent::__construct($message, $code);
		$this->httpCode = intval($httpCode);
	}
	
	
}

?>