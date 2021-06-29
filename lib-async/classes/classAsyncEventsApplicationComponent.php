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

class AsyncEventsApplicationComponent extends ApplicationComponent {
	
	const STAGE = 95;
	
	/**
	 * @var AsyncEventWrapper[]
	 */
	private $eventProcessQueue = array();
	private $eventProcessing = false;
	
	//************************************************************************************
	public function getName() { return 'async'; }
	public function getStages() { return array(self::STAGE); }
	
	//************************************************************************************
	/**
	 * @return IAsyncEventsConsumer[]
	 */
	public function getConsumers() {
		return $this->getExtensions('IAsyncEventsConsumer');
	}
	
	//************************************************************************************
	/**
	 * @return IAsyncEventsDispatcher[]
	 */
	public function getDispatchers() {
		return $this->getExtensions('IAsyncEventsDispatcher');
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if ($stage == self::STAGE && $this->getApplicationContext()->isEnvironmentCLI() && $this->getApplicationContext()->tagContains('async')) {
			
			if ($this->getApplicationContext()->getCLIArgumentByName('noinput')) {
				$eventData = '';
			} else {
				$eventData = $this->getApplicationContext()->readFullStdIn();
			}
			
			$eventType = $this->getApplicationContext()->getCLIArgumentByName('eventType');
			$oEvent = AsyncEventWrapper::CreateFromRaw($eventType, $eventData);
			if ($oEvent->getEventType()) {
				$this->processEvent($oEvent);
			}
		}
	}

	
	//************************************************************************************
	/**
	 * @param AsyncEventWrapper $oEvent
	 */
	public function processEvent($oEvent) {
		if (!($oEvent instanceof AsyncEventWrapper)) throw new InvalidArgumentException('oEvent is not AsyncEventWrapper');
		$this->eventProcessQueue[] = $oEvent;
		
		if ($this->eventProcessing) return;
		
		$this->eventProcessing = true;
		$iterNum = 0;
		while($this->eventProcessQueue) {
			$oEvent = array_shift($this->eventProcessQueue);
			
			foreach($this->getConsumers() as $oConsumer) {
				try {
					$res = $oConsumer->consume($oEvent);
					if ($res == IAsyncEventsConsumer::RET_STOP_EXECUTION) break;
				} catch(Exception $e) {
					Logger::warn(sprintf("Consuming async event %s by consumer %s", $oEvent->getEventType(), get_class($oConsumer)), $e);
				}
			}
			
			$iterNum += 1;
			if ($iterNum > 20) {
				Logger::warn("Async events queue not getting empty. Aborting queue execution.");
				break;
			}
		}
		$this->eventProcessing = false;
	}
	
	
	//************************************************************************************
	/**
	 * @param int $delay
	 * @param string|array|JsonSerializable $event
	 * @param string $tag
	 */
	public function dispatchEvent($delay, $event, $tag='') {
		if (!is_string($event) && !is_array($event) && !($event instanceof JsonSerializable)) throw new InvalidArgumentException('event is not string, array nor JsonSerializable');
		if ($delay < 5) $delay = 5;
		if (!$tag) $tag = 'async_event';

		$oDispatcher = null;
		foreach($this->getDispatchers() as $e) {
			if ($e->matches($delay, $event, $tag)) {
				$oDispatcher = $e;
				break;
			}
		}

		if ($oDispatcher) {
			$oDispatcher->dispatch($delay, $event, $tag);
		} else {
			throw new IllegalStateException('Could not find dispatcher for given event');
		}
	}
	
	//************************************************************************************
	/**
	 * @param int $delay
	 * @param Closure $oClosure
	 */
	public function dispatchClosure($delay, $oClosure, $tag='') {
		if (!($oClosure instanceof Closure)) throw new InvalidArgumentException('oClosure is not Closure');
		
		$this->dispatchEvent($delay, new AsyncEventRunnable_Closure($oClosure), $tag);
	}
	
}

?>