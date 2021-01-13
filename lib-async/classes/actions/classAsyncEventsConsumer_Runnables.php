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

class AsyncEventsConsumer_Runnables implements IAsyncEventsConsumer {

	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() {
		return 100;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		
	}

	//************************************************************************************
	/**
	 * @param AsyncEventWrapper $oEvent
	 * @return int
	 */
	public function consume($oEvent) {
		if ($oEvent->isDataObject() && ($oEvent->getDataObject() instanceof IAsyncEventRunnable)) {
			try {
				$oEvent->getDataObject()->run();
			} catch(Exception $e) {
				Logger::warn("AsyncEventsConsumer_Runnables::consume", $e);
			}
			
			return self::RET_STOP_EXECUTION;
		}
		
		return self::RET_CONTINUE_EXECUTION;
	}
	
}

?>