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


class HTTPApplicationComponent extends ApplicationComponent {

	const STAGE_INIT = 10;
	const STAGE_FINISH = 1000;
	
	//************************************************************************************
	public function getStages() {
		return array(self::STAGE_INIT, self::STAGE_FINISH);
	}

	//************************************************************************************
	public function getName() {
		return 'http';
	}

	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param int $stage
	 */
	public function onInit($stage) {
		
	}

	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if (!$this->getApplicationContext()->isEnvironmentWeb()) return;
		
		if ($stage == self::STAGE_INIT) {
			$oRequest = new HTTPServerRequestPHP($this->getConfig());
			$oResponse = new HTTPServerResponsePHP();
			
			$oResponse->getHeaders()->putSingle('Server', '');
			$oResponse->getHeaders()->putSingle('X-Powered-By', 'PHP+pregusiaFW');
			
			$this->registerService('IHTTPServerRequest', '', $oRequest);
			$this->registerService('IHTTPServerResponse', '', $oResponse);
		}
		
		if ($stage == self::STAGE_FINISH) {
			$oResponse = $this->getService('IHTTPServerResponse');
			false && $oResponse = new IHTTPServerResponse();
			
			if ($oResponse instanceof HTTPServerResponsePHP) {
				$oResponse->process();
			}
		}
	}
	
	
}

?>