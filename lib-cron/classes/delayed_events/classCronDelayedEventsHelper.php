<?php

class CronDelayedEventsHelper {
	
	private $oComponent = null;
	
	//************************************************************************************
	/**
	 * @return ApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }

	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() {
		return $this->getComponent()->getApplicationContext();
	}
	
	//************************************************************************************
	public function __construct($oComponent) {
		if (!($oComponent instanceof ApplicationComponent)) throw new InvalidArgumentException('oComponent is not ApplicationComponent');
		$this->oComponent = $oComponent;
	}
	
	//************************************************************************************
	/**
	 * @return Configuration
	 */
	private function getConfig() {
		$oConfig = $this->getApplicationContext()->getConfig()->getSubConfig('async.cron.delayedEvents');
		//if ($oConfig->isEmpty()) throw new ConfigurationException('Cron delayed actions is not configured (entry async.cron.delayedActions)');
		return $oConfig;
	}
	
	//************************************************************************************
	/**
	 * @return SQLStorage
	 */
	private function getStorage() {
		if ($this->getConfig()->isEmpty()) return null;
		
		$oStorageComponent = $this->getApplicationContext()->getComponent('storage.sql', false);
		false && $oStorageComponent = new SQLStorageApplicationComponent();
		
		if ($oStorageComponent) {
			return $oStorageComponent->getStorage($this->getConfig()->getValue('storageName'));
		}
		
		return null;
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	private function getTableName() {
		if ($this->getConfig()->isEmpty()) {
			return 'cron_delayed_events';
		} else {
			return $this->getConfig()->getValue('tableName');
		}
	}
	
	//************************************************************************************
	public function getEventsToRun() {
		$oStorage = $this->getStorage();
		$tableName = $this->getTableName();
		$events = array();
		
		if ($oStorage && $tableName) {
			$now = $this->getApplicationContext()->getTimestamp();
			$actions = array();
			$query = sprintf('SELECT * FROM %s WHERE runTime <= %d', $tableName, $now);
			
			foreach($oStorage->getRecords($query) as $oRow) {
				$eventType = $oRow->getColumn('eventType')->getValueMapped();
				$eventData = $oRow->getColumn('eventData')->getValueMapped();
				
				$events[] = AsyncEventWrapper::CreateFromRaw($eventType, $eventData);
			}
			
			$oStorage->query(sprintf('DELETE FROM %s WHERE runTime <= %d', $tableName, $now));
		}
		
		return $events;
	}
	
	//************************************************************************************
	/**
	 * @param int $delay
	 * @param AsyncEventWrapper $oEvent
	 */
	public function scheduleEvent($delay, $oEvent) {
		if (!($oEvent instanceof AsyncEventWrapper)) throw new InvalidArgumentException('oEvent is not AsyncEventWrapper');
		
		$delay = intval($delay);
		if ($delay < 0) $delay = 0;
		
		$oStorage = $this->getStorage();
		$tableName = $this->getTableName();
		
		if ($oStorage && $tableName) {
			
			$oStorage->insertRecord($tableName, array(
				'runTime' => $this->getApplicationContext()->getTimestamp() + $delay,
				'eventType' => $oEvent->getEventType(),
				'eventData' => $oEvent->getDataRaw()	
			));
			
		} else {
			throw new IllegalStateException('DelayedActions not enabled');
		}	
	}	
	
}

?>