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


class CacheApplicationComponnet extends ApplicationComponent {
	
	const STAGE = 10;
	
	private $caches = array();
	
	//************************************************************************************
	public function getName() { return 'cache'; }
	public function getStages() { return array(self::STAGE); }
	public function onProcess($stage) { }
	
	//************************************************************************************
	public function onInit($stage) {
		if ($stage == self::STAGE) {
			$this->caches['runtime'] = new CacheMechanism_RuntimeMemory($this->getApplicationContext(), 'runtime');
		}
	}
	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return ICacheMechanism
	 */
	public function getCache($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		if (!$this->caches[$name]) {
			$oConfig = $this->getConfig()->getSubConfig($name);
			if ($oConfig->isEmpty()) {
				throw new InvalidArgumentException(sprintf('Cache with name "%s" not found', $name));
			}
			
			$className = $oConfig->getValue('className');
			if (!$className) throw new ConfigurationException(sprintf('className field in config of cache "%s" is empty', $name));
			
			$oClass = CodeBase::getClass($className, false);
			if (!$oClass) throw new ConfigurationException(sprintf('Could not find class "%s" in config of cache "%s"', $className, $name));
			if (!$oClass->isImplementing('ICacheMechanism')) throw new ConfigurationException(sprintf('Class "%s" is not implementing ICacheMechanism (in config of cache "%s")', $className, $name));
			if (!$oClass->hasStaticMethod('Create')) throw new ConfigurationException(sprintf('Method %s::Create not exists  (in config of cache "%s")', $className, $name));

			$obj = $oClass->callStaticMethod('Create', array($this->getApplicationContext(), $oConfig));
			$this->caches[$name] = $obj;
		}
		
		return $this->caches[$name];
	}
	
}

?>