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


class WebResponseRedirect extends WebResponseBase {
	
	private $location = '';

	//************************************************************************************
	public function __construct($location='') {
		$this->location = $location;
	}
	
	//************************************************************************************
	/**
	 * @param IHTTPServerResponse $oHttpResponse
	 */
	public function finish($oHttpResponse) {
		$loc = $this->location;
		if (!$loc) {
			$oDispatcher = ApplicationContext::getCurrent()->getService('WebDispatcher');
			false && $oDispatcher = new WebDispatcher();
			
			if (!$oDispatcher) throw new IllegalStateException('WebDispatcher service not found int application context');
			
			$loc = $oDispatcher->getBaseLocations()->getLocation();
		}
		
		$oHttpResponse->getHeaders()->putSingle('Location', $loc);
	}

}

?>