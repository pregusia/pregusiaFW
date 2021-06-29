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


/**
 * 
 * @author pregusia
 * @NeedLibrary lib-cli
 *
 */
class CLICommandHandler_DBTest implements ICLICommandHandler {
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getCommandName() {
		return 'db:test';
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getDescription() {
		return 'Test SQL connection to given storage';
	}
	
	//************************************************************************************
	/**
	 * @return CLIArgumentBinder[]
	 */
	public function getArguments() {
		return array(
			new CLIArgumentBinder('storageName', true, 'Storage name')	
		);
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param array $arguments
	 * @return int
	 */
	public function handleCommand($oContext, $arguments) {
		$oComponent = $oContext->getComponent('storage.sql');
		false && $oComponent = new SQLStorageApplicationComponent();
		
		$oStorage = $oComponent->getStorage($arguments['storageName']); 
		if (!$oStorage) {
			throw new ObjectNotFoundException(sprintf('Cannot find SQLStorage %s', $arguments['storageName']));
		}
		
		try {
			$oStorage->query('SHOW TABLES');
			printf("SQL CONNECTION OK\n");
			return 0;
			
		} catch(Exception $e) {
			
			printf("ERROR: %s\n", UtilsExceptions::toString($e));
			return 1;
		}
	}	
	
}

?>