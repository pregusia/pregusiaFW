<?php

class AsyncEventsDispatcher_CronDelayedEvents implements IAsyncEventsDispatcher {
	
	private $oComponent = null;
	private $oHelper = null;
	
	
	//************************************************************************************
	/**
	 * @return AsyncEventsApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	/**
	 * @return CronDelayedEventsHelper
	 */
	public function getHelper() { return $this->oHelper; }
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() {
		return 10;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		$this->oComponent = $oComponent;
		$this->oHelper = new CronDelayedEventsHelper($oComponent);
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getMinDelay() {
		$val = intval($this->getComponent()->getConfig()->getValue('cron.delayedEvents.minDelay'));
		
		if ($val <= 0) $val = 2 * 60;
		return $val;
	}

	//************************************************************************************
	/**
	 * @param int $delay
	 * @param mixed $event
	 * @param string $tag
	 * @return bool
	 */
	public function matches($delay, $event, $tag) {
		if ($delay >= $this->getMinDelay()) {
			return true;
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @param int $delay
	 * @param mixed $event
	 * @param string $tag
	 * @return bool
	 */
	public function dispatch($delay, $event, $tag) {
		$oEvent = AsyncEventWrapper::CreateFromEvent('async_event', $event);
		$this->getHelper()->scheduleEvent($delay, $oEvent);
		return true;
	}
	
}

?>