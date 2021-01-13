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

class ApplicationContext {
	
	private static $instance = null;
	
	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public static function getCurrent() {
		if (!self::$instance) throw new IllegalStateException('Application context is not initialized');
		return self::$instance;
	}
	
	
	
	
	
	
	const ENV_UNKNOWN = 0;
	const ENV_WEB = 1;
	const ENV_CLI = 2;
	
	
	private $appPath = '';
	private $envType = 0;
	private $timestamp = 0;
	private $oConfig = null;
	private $tags = array();
	
	/**
	 * @var ApplicationComponent[]
	 */
	private $components = array();
	
	private $services = array();
	
	/**
	 * @var ApplicationComponent[]
	 */
	private $disabledComponents = array();
	
	//************************************************************************************
	/**
	 * @return Configuration
	 */
	public function getConfig() { return $this->oConfig; }
	
	//************************************************************************************
	public function __construct($tags = array()) {
		if (self::$instance) throw new IllegalStateException('ApplicationContext already created');
		self::$instance = $this;
		
		foreach($tags as $tag) {
			$this->tagSet($tag);
		}
		
		$this->appPath = rtrim(getcwd(),'/') . '/';
		$this->timestamp = time();
		
		$this->initExecutor();
		$this->initComponents();
		$this->initConfiguration();
		$this->initLocalServices();

		// logger adapter
		if ($this->getConfig()->getPath('Logger.path')) {
			$oConfig = $this->getConfig();
			Logger::registerAdapter(function($str, $type, $msg, $obj, $fields) use ($oConfig) {
				file_put_contents($oConfig->getPath('Logger.path'), $str . "\n", FILE_APPEND);
			});
		}
		
		$self = $this;
		$this->forEachComponent(function($stage, $oComponent) use($self) {
			$oComponent->onInit($stage);
		});
		
		foreach($this->services as $oService) {
			if ($oService instanceof IApplicationAutoLocalService) {
				$oService->onInit($this);
			}
		}
	}
	
	//************************************************************************************
	private function initLocalServices() {
		foreach(CodeBase::getClassesImplementing('IApplicationAutoLocalService') as $oClass) {
			if ($oClass->isAbstract()) continue;
			
			$oInstance = $oClass->ctorCreate();
			false && $oInstance = new IApplicationAutoLocalService();
			
			$this->registerService($oInstance->getServiceClassName(), $oInstance->getServiceName(), $oInstance);
		}
	}
	
	//************************************************************************************
	private function initExecutor() {
		$n = php_sapi_name();
		if ($n == 'cli') {
			$this->envType = self::ENV_CLI;
		}
		elseif ($n == 'apache2handler' || $n == 'fpm-fcgi') {
			$this->envType = self::ENV_WEB;
		}
		else {
			throw new CoreException('Invalid SAPI name - ' . $n);
		}		
	}
	
	//************************************************************************************
	private function initComponents() {
		foreach(CodeBase::getClassesExtending('ApplicationComponent') as $oClass) {
			$oComponent = $oClass->getInstance();
			false && $oComponent = new ApplicationComponent();
			
			$this->components[] = $oComponent;
			$oComponent->internalSetApplicationContext($this); 
		}
	}
	
	//************************************************************************************
	public function forEachComponent($func) {
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		
		$arr = array();
		foreach($this->components as $oComponent) {
			foreach($oComponent->getStages() as $stage) {
				$arr[] = array($stage, $oComponent);
			}
		}
		
		usort($arr, function($a, $b){
			return $a[0] - $b[0];
		});
		
		foreach($arr as $v) {
			$func($v[0], $v[1]);
		}
	}
	
	//************************************************************************************
	public function disableComponent($name) {
		$oComponent = $this->getComponent($name, false);
		if ($oComponent) {
			$this->disabledComponents[] = $oComponent;
		}
	}
	
	//************************************************************************************
	public function shouldProcess($oComponent) {
		if (!($oComponent instanceof ApplicationComponent)) throw new InvalidArgumentException('oComponent is not ApplicationComponent');
	
		foreach($this->disabledComponents as $c) {
			if ($c === $oComponent) {
				return false;
			}
		}
		
		return true;
	}
	
	//************************************************************************************
	private function initConfiguration() {
		$this->oConfig = new Configuration($this);
		
		// configi z bibliotek
		foreach(CodeBase::getLibraries() as $oLibrary) {
			if ($oLibrary->exists('configuration')) {
				$this->oConfig->loadDirectory($oLibrary->realPath('configuration/'));
			}
		}
		
		// config glowny
		$this->oConfig->loadDirectory($this->adaptPath('configuration', true));
		
		// NOTE: config z DB jest ladowany przez component od DB
	}

	//************************************************************************************
	public function isEnvironmentCLI() { return $this->envType == self::ENV_CLI; }
	public function isEnvironmentWeb() { return $this->envType == self::ENV_WEB; }
	
	//************************************************************************************
	/**
	 * @param int $n
	 * @return string
	 */
	public function getCLIArgumentByIndex($n) {
		global $argv;
		return $argv[$n];
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public function getCLIArgumentByName($name) {
		global $argv;
		$args = array();
		foreach($argv as $p) {
			list($k,$v) = explode('=',$p,2);
			if ($k && $v && $k == $name) {
				return $v;
			}
		}
		return '';
	}
	
	//************************************************************************************
	public function tagContains($tag) { return $this->tags[$tag] ? true : false; }
	public function tagSet($tag) { $this->tags[$tag] = 1; }
	public function tagUnset($tag) { unset($this->tags[$tag]); }
	
	//************************************************************************************
	public function readFullStdIn() {
		if ($this->isEnvironmentCLI()) {
			return file_get_contents("php://stdin");
		}
		if ($this->isEnvironmentWeb()) {
			return file_get_contents("php://input");
		}
		return '';
	}
	
	//************************************************************************************
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	//************************************************************************************
	public function adaptPath($path, $directory=false) {
		if (substr($path,0,1) == '/') {
			$res = $path;
		} else {
			$res = $this->appPath . $path;
		}
		if ($directory) {
			$res = rtrim($res,'/') . '/';
		}
		return $res;
	}
	
	//************************************************************************************
	public function registerService($clsName, $name, $inst) {
		if (!$inst) throw new InvalidArgumentException('Service instance is null');
			
		$key = $clsName;
		if ($name) {
			$key .= '.' . $name;
		}
		if ($this->services[$key]) {
			throw new InvalidArgumentException(sprintf('Service %s already registered', $key));
		}
		$this->services[$key] = $inst;
		return $inst;
	}
	
	//************************************************************************************
	/**
	 * @param string $clsName
	 * @return <$clsName>
	 */
	public function getService($clsName, $name='') {
		$key = $clsName;
		if ($name) {
			$key .= '.' . $name;
		}
		
		return $this->services[$key];
	}

	//************************************************************************************
	/**
	 * @return ApplicationComponent
	 */
	public function getComponent($name, $throwException=true) {
		foreach($this->components as $oComponent) {
			if ($oComponent->getName() == $name) return $oComponent;
		}
		if ($throwException) {
			throw new FrameworkException(sprintf('Component %s not found', $name));
		} else {
			return null;
		}
	}
	
}

?>