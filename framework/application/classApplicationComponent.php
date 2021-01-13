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

abstract class ApplicationComponent {
	
	/**
	 * @var ApplicationContext
	 */
	private $oContext = null;
	
	/**
	 * @var IApplicationComponentExtension[]
	 */
	private $extensions = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public abstract function getName();
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public abstract function getStages();
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public abstract function onInit($stage);

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public abstract function onProcess($stage);
	

	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 */
	public final function internalSetApplicationContext($oContext) {
		if (!($oContext instanceof ApplicationContext)) throw new InvalidArgumentException('oContext is not ApplicationContext');
		$this->oContext = $oContext;
	}

	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() {
		return $this->oContext;
	}
	
	//************************************************************************************
	/**
	 * Zwraca podzbior konfiguracji zaczynajacy sie od nazwy tego komponentu
	 * @return Configuration
	 */
	public function getConfig() {
		return $this->getApplicationContext()->getConfig()->getSubConfig($this->getName());
	}
	
	//************************************************************************************
	/**
	 * @param string $ifaceName
	 * @return IApplicationComponentExtension[]
	 */
	public function getExtensions($ifaceName) {
		if (!isset($this->extensions[$ifaceName])) {
			$oInterface = CodeBase::getInterface($ifaceName);
			if (!$oInterface->isExtending('IApplicationComponentExtension')) throw new InvalidArgumentException(sprintf('Interface %s is not extending IApplicationComponentExtension', $ifaceName));
			$this->extensions[$ifaceName] = array();
			
			$arr = $oInterface->getAllInstances();
			usort($arr, function($a, $b) {
				return $a->getPriority() - $b->getPriority();
			});
			
			foreach($arr as $oExtension) {
				$oExtension->onInit($this);
				$this->extensions[$ifaceName][] = $oExtension;
			}
		}
		
		return $this->extensions[$ifaceName];
	}
	
	//************************************************************************************
	public function registerService($clsName, $name, $inst) {
		return $this->getApplicationContext()->registerService($clsName, $name, $inst);
	}
	
	//************************************************************************************
	public function getService($clsName, $name='') {
		return $this->getApplicationContext()->getService($clsName, $name);
	}
	
}

?>