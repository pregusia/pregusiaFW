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


class CLICommandsHandlerApplicationComponent extends ApplicationComponent {
	
	
	const STAGE = 95;
	
	//************************************************************************************
	public function getName() { return 'cli'; }
	public function getStages() { return array(self::STAGE); }
	
	//************************************************************************************
	public function onInit($stage) {
		
	}
	
	//************************************************************************************
	/**
	 * @return ICLICommandHandler[]
	 */
	private function getHandlers() {
		return CodeBase::getInterface('ICLICommandHandler')->getAllInstances();		
	}
	
	//************************************************************************************
	public function onProcess($stage) {
		if ($stage == self::STAGE && $this->getApplicationContext()->tagContains('cli')) {
			
			$commandName = $this->getApplicationContext()->getCLIArgumentByIndex(1);
			if (!$commandName) {
				printf("ERROR: No command given\n");
				$this->printCommands();
				exit(1);
			}
			
			
			foreach($this->getHandlers() as $oHandler) {
				if ($oHandler->getCommandName() == $commandName) {
					$rt = $this->runCommand($oHandler);
					exit($rt);
				}
			}

			printf("ERROR: Command not found\n");
			$this->printCommands();
			exit(1);
		}
	}
	
	//************************************************************************************
	private function printCommands() {
		printf("USAGE: ./cli <command_name>\n");
		printf("\n");
		printf("AVAILABLE COMMANDS:\n");
		
		$maxLen = 0;
		foreach($this->getHandlers() as $oHandler) {
			if (strlen($oHandler->getCommandName()) > $maxLen) {
				$maxLen = strlen($oHandler->getCommandName()); 
			}
		}
		
		foreach($this->getHandlers() as $oHandler) {
			printf("  %s%s%s\n",
				$oHandler->getCommandName(),
				str_repeat(' ', $maxLen - strlen($oHandler->getCommandName()) + 3),
				$oHandler->getDescription()
			);
		}
		printf("\n");
	}
	
	//************************************************************************************
	/**
	 * @param ICLICommandHandler $oHandler
	 */
	private function printUsage($oHandler) {
		$parts = array();
		$parts[] = $oHandler->getCommandName();
		
		foreach($oHandler->getArguments() as $oArgument) {
			if ($oArgument->isRequired()) {
				$parts[] = sprintf('%s=...', $oArgument->getName());
			}
		}
		foreach($oHandler->getArguments() as $oArgument) {
			if (!$oArgument->isRequired()) {
				$parts[] = sprintf('[%s=...]', $oArgument->getName());
			}
		}
		
		$maxLen = 0;
		foreach($oHandler->getArguments() as $oArgument) {
			if (strlen($oArgument->getName()) > $maxLen) {
				$maxLen = strlen($oArgument->getName());
			}
		}
		
		printf("USAGE: ./cli %s\n", implode(' ',$parts));
		printf("DESCRIPTION: %s\n", $oHandler->getDescription());
		printf("\n");
		foreach($oHandler->getArguments() as $oArgument) {
			printf("  %s%s- %s\n",
				$oArgument->getName(),
				str_repeat(' ', $maxLen - strlen($oArgument->getName()) + 2), 
				$oArgument->getDescription()
			);
		}
		printf("\n");
	}

	//************************************************************************************
	/**
	 * @param ICLICommandHandler $oHandler
	 */
	private function runCommand($oHandler) {
		$arguments = array();
		$ok = true;
		
		foreach($oHandler->getArguments() as $oArgument) {
			$val = trim($this->getApplicationContext()->getCLIArgumentByName($oArgument->getName()));
			$arguments[$oArgument->getName()] = $val;
			
			if ($oArgument->isRequired() && !$val) {
				$ok = false;
			}
		}
		
		if (!$ok) {
			$this->printUsage($oHandler);
			return 1;
		}
		
		return $oHandler->handleCommand($this->getApplicationContext(), $arguments);
	}
	
	
	
}

?>