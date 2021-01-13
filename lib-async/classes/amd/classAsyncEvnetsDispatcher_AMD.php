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

class AsyncEventsDispatcher_AMD implements IAsyncEventsDispatcher {
	
	private $oComponent = null;
	
	//************************************************************************************
	/**
	 * @return AsyncEventsApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() { 
		return 100;
	}
	
	//************************************************************************************
	public function getHost() { return $this->getComponent()->getConfig()->getValue('amd.host'); }
	public function getPort() { return intval($this->getComponent()->getConfig()->getValue('amd.port')); }
	public function getSecret() { return $this->getComponent()->getConfig()->getValue('amd.secret'); }
	public function getArea() { return $this->getComponent()->getConfig()->getValue('amd.area'); }
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isConfigured() {
		if (!$this->getHost()) return false;
		if (!$this->getPort()) return false;
		if (!$this->getSecret()) return false;
		if (!$this->getArea()) return false;
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		$this->oComponent = $oComponent;
	}

	//************************************************************************************
	/**
	 * @param int $delay
	 * @param mixed $oEvent
	 * @param string $tag
	 * @return bool
	 */
	public function matches($delay, $event, $tag) {
		return $this->isConfigured();
	}
	
	//************************************************************************************
	/**
	 * @param int $delay
	 * @param mixed $event
	 * @param string $tag
	 * @return bool
	 */
	public function dispatch($delay, $event, $tag) {
		if (!$this->isConfigured()) return false;		
		
		$delay = intval($delay);
		if ($delay < 5) $delay = 5;

		$msg = array(
			'area' => $this->getArea(),
			'secret' => $this->getSecret(),
			'delay' => $delay,
			'tag' => $tag,
		);
		
		if (is_string($event)) $msg['message'] = strval($event);
		elseif (is_array($event)) $msg['message'] = $event;
		elseif ($event instanceof JsonSerializable) $msg['message'] = UtilsJSON::serializeAbstraction($event);
		else throw new InvalidArgumentException('event could not be serialized');
		
		$errorNr = 0;
		$errorText = '';
		
		$fp = @fsockopen($this->getHost(), $this->getPort(), $errorNr, $errorText, 5);
		if ($fp === false) throw new IOException(sprintf('Could not connect to AMD instance (%d: %s)', $errorNr, $errorText));
		
		fprintf($fp, "%s\n", json_encode($msg));
		fclose($fp);
		return true;
	}
	
}

?>