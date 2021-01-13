<?php

class AsyncEventsConsumer_CronDelayedEvents implements IAsyncEventsConsumer {
	
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
	 * @param AsyncEventWrapper $oEvent
	 * @return int
	 */
	public function consume($oEvent) {
		if ($oEvent->getEventType() == 'cron.minutes1') {
			foreach($this->getHelper()->getEventsToRun() as $e) {
				$this->getComponent()->processEvent($e);
			}
		}
		return self::RET_CONTINUE_EXECUTION;
	}
	
}

?>