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


class RemoteServiceException extends Exception {
	
	private $errorData = array();
	
	//************************************************************************************
	public function infoFieldsArray() {
		return $this->errorData['data'];
	}
	
	//************************************************************************************
	public function __construct($arg=array(),$code=0) {
		$this->errorData = array(
			'data' => array(),
			'code' => $code,
			'message' => 'RemoteServiceException'
		);
		
		if (is_array($arg)) {
			$this->errorData = $arg;
			
			if ($arg['code']) $code = $arg['code'];
			$msg =  $arg['message'] ? $arg['message'] : sprintf('Error #%d', $code);
			
			parent::__construct($msg, $code);
		}
		elseif (is_string($arg)) {
			parent::__construct(trim($arg), $code);
		}
		else {
			parent::__construct('RemoteServiceException', $code);
		}
	}
	
}

?>