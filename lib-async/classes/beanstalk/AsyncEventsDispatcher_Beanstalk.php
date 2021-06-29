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

class AsyncEventsDispatcher_Beanstalk implements IAsyncEventsDispatcher {
	
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
		return 90;
	}
	
	//************************************************************************************
	public function getHost() { return $this->getComponent()->getConfig()->getValue('beanstalk.host'); }
	public function getPort() { return intval($this->getComponent()->getConfig()->getValue('beanstalk.port')); }
	public function getTube() {
		$val = $this->getComponent()->getConfig()->getValue('beanstalk.tube');
		if (!$val) $val = 'default';
		return $val;
	}
	
	//************************************************************************************
	public function getTTR() {
		$val = intval($this->getComponent()->getConfig()->getValue('beanstalk.ttr'));
		if (!$val) $val = 5 * 60;
		return $val;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isConfigured() {
		if (!$this->getHost()) return false;
		if (!$this->getPort()) return false;
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
		return $this->isConfigured() && $tag == 'beanstalk';
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
		if ($tag != 'beanstalk') return false;

		if (is_string($event)) {
			$tmp = $event;
			$event = array();
			$event['message'] = $tmp;
		}
		elseif (is_array($event)) {
			// ok
		}
		elseif ($event instanceof JsonSerializable) {
			$event = UtilsJSON::serializeAbstraction($event);
		}
		else throw new InvalidArgumentException('event could not be serialized');
		
		$tube = $this->getTube();
		if ($event['tube']) $tube = $event['tube'];
		
		$delay = intval($delay);
		if ($delay < 5) $delay = 5;

		
		$msg = array();
		foreach($event as $k => $v) $msg[$k] = $v;
		$msg['tag'] = $tag;
		

		
		$errorNr = 0;
		$errorText = '';
		
		$fp = @fsockopen($this->getHost(), $this->getPort(), $errorNr, $errorText, 5);
		if ($fp === false) throw new IOException(sprintf('Could not connect to BeanStak instance (%d: %s)', $errorNr, $errorText));
		
		$msg = json_encode($msg);
		
		$cmd = sprintf("put 100 %d %d %d\r\n", $delay, $this->getTTR(), strlen($msg));
		
		fprintf($fp, "use %s\r\n", $tube);
		fprintf($fp, "put 100 %d %d %d\r\n", $delay, $this->getTTR(), strlen($msg));
		fprintf($fp, "%s\r\n", $msg);
		fflush($fp);
		sleep(1);
		
		//$res = fread($fp, 128);
		fclose($fp);
		
// 		Logger::debug('AsyncEventsDispatcher_Beanstalk::dispatch',array(
// 			'msg' => $msg,
// 			'cmd' => $cmd,
// 			'res' => $res
// 		));
		
		
		return true;
	}
	
}

?>