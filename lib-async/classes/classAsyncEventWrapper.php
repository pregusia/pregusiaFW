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

class AsyncEventWrapper {

	const DATA_RAW = 1;
	const DATA_ARRAY = 2;
	const DATA_OBJECT = 3;
	
	private $eventType = '';
	
	
	private $dataType = 1;
	private $dataRaw = "";
	private $dataArray = null;
	private $dataObject = null;
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getEventType() { return $this->eventType; }
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getDataType() { return $this->dataType; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getDataRaw() { return $this->dataRaw; }
	
	//************************************************************************************
	/**
	 * @return array
	 */
	public function getDataArray() { return $this->dataArray; }
	
	//************************************************************************************
	/**
	 * @return object
	 */
	public function getDataObject() { return $this->dataObject; }
		
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isDataObject() {
		return $this->dataType == self::DATA_OBJECT;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isDataArray() {
		return $this->dataType == self::DATA_ARRAY;
	}
	
	//************************************************************************************
	/**
	 * @param string $eventType
	 * @param string $eventData
	 */
	private function __construct($eventType, $eventData) {
		$this->eventType = $eventType;

		$eventData = trim($eventData);
		
		$this->dataRaw = $eventData;
		$this->dataType = self::DATA_RAW;
		
		if ($eventData) {
			$arr = @json_decode($eventData, true);
			if (is_array($arr)) {
				
				$this->dataArray = $arr;
				$this->dataType = self::DATA_ARRAY;
				
				$eventObject = UtilsJSON::unserializeAbstraction($arr);
				if ($eventObject) {
					$this->dataObject = $eventObject;
					$this->dataType = self::DATA_OBJECT;
				}
			}
		}
		
		if (!$this->eventType && $this->dataArray && isset($this->dataArray['__eventType'])) {
			$this->eventType = $this->dataArray['__eventType'];
		}
		
	}
	
	//************************************************************************************
	/**
	 * @param string $eventType
	 * @param string $eventData
	 * @return AsyncEventWrapper
	 */
	public static function CreateFromRaw($eventType, $eventData) {
		return new AsyncEventWrapper($eventType, $eventData);
	}
	
	//************************************************************************************
	/**
	 * @param string $eventType
	 * @param string,array,JsonSerializable $event
	 * @return AsyncEventWrapper
	 */
	public static function CreateFromEvent($eventType, $event) {
		$obj = new AsyncEventWrapper($eventType, '');

		if ($event instanceof JsonSerializable) {
			$arr = UtilsJSON::serializeAbstraction($event);
			
			$obj->dataType = self::DATA_OBJECT;
			$obj->dataObject = $event;
			$obj->dataArray = $arr;
			$obj->dataRaw = json_encode($arr);
			return $obj;
		}
		if (is_array($event)) {
			$obj->dataType = self::DATA_ARRAY;
			$obj->dataObject = null;
			$obj->dataArray = $event;
			$obj->dataRaw = json_encode($event);
			return $obj;
		}
		if (is_string($event)) {
			$obj->dataType = self::DATA_RAW;
			$obj->dataObject = null;
			$obj->dataArray = array();
			$obj->dataRaw = $event;
			return $obj;			
		}
		
		throw new InvalidArgumentException('Given event is not string,array nor JsonSerializable');
	}
	
	
}

?>