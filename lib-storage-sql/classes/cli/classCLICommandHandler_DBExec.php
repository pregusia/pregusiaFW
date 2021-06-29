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
class CLICommandHandler_DBExec implements ICLICommandHandler {
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getCommandName() {
		return 'db:exec';
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getDescription() {
		return 'Exec SQL commands from stdin to given storage';
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
		
		$input = $oContext->readFullStdIn();
		$queries = array();
		$curr = array();
		
		foreach(explode("\n", $input) as $line) {
			$line = trim($line);
			if (!$line) continue;
			if (UtilsString::startsWith($line, '#')) continue;
			
			$curr[] = $line;
			if (UtilsString::endsWith($line, ';')) {
				$queries[] = implode("\n", $curr);
				$curr = array();
			}
		}
		
		if ($curr) {
			$queries[] = implode("\n", $curr);
		}
		
		
		foreach($queries as $query) {
			printf("%s\n", $query);
			try {
				$oStorage->query($query);
				printf("OK\n");
			} catch(Exception $ex) {
				printf("ERROR: %s\n", UtilsExceptions::toString($ex));
				return 1;
			}
			printf("\n");
		}

		return 0;
	}	
	
}

?>