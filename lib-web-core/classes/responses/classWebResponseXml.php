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


class WebResponseXml extends WebResponseContentAppendable {
	
	//************************************************************************************
	public function __construct($content='') {
		parent::__construct($content);
	}

	//************************************************************************************
	/**
	 * @param IHTTPServerResponse $oHttpResponse
	 */
	public function finish($oHttpResponse) {
		$oHttpResponse->setContentType('text/xml; charset=UTF-8');
		$oHttpResponse->setStatusCode($this->getHttpCode());
		
		$self = $this;
		$oHttpResponse->pushOutputFunction(function() use ($self){
			print('<?xml version="1.0" encoding="UTF-8" ?>');
			print("\n");
			print($self->content);
		});
	}

}

?>