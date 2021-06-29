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


class Configuration {
	
	/**
	 * @var ApplicationContext
	 */
	private $oContext = null;
	private $config = array();

	//************************************************************************************
	public function __construct($oContext) {
		if (!($oContext instanceof ApplicationContext)) throw new InvalidArgumentException('oContext is not ApplicationContext');
		$this->oContext = $oContext;
	}
	
	//************************************************************************************
	public function getValue($name) {
		if (!$name) throw new InvalidArgumentException('Name cannot be empty !');
		
		$tmp = $this->config;
		foreach(explode('.',$name) as $p) {
			if (is_array($tmp) && isset($tmp[$p])) {
				$tmp = $tmp[$p];
			} else {
				$tmp = null;
			}
		}
		return $tmp;
	}
	
	//************************************************************************************
	public function getPath($name,$directory=false) {
		$val = $this->getValue($name);
		if ($val) {
			return $this->oContext->adaptPath($val, $directory);
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasKey($name) {
		return isset($this->config[$name]);
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isEmpty() {
		return count($this->config) == 0;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return Configuration
	 */
	public function getSubConfig($name) {
		$arr = $this->getArray($name);
		$oConfig = new Configuration($this->oContext);
		if (is_array($arr)) {
			$oConfig->config = $arr;
		}
		return $oConfig;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @throws ConfigEntryInvalidValueException
	 * @return CodeBaseDeclaredClass
	 */
	public function getClass($name, $throwException=true) {
		$val = $this->getValue($name);
		if (!$val) {
			if ($throwException) {
				throw new ConfigEntryInvalidValueException($name);
			} else {
				return null;
			}
		}
		return CodeBase::getClass($val, $throwException);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $defaultClassName
	 * @return <T>
	 */
	public function getClassInstance($name, $defaultClassName='') {
		$oClass = $this->getClass($name, false);
		if ($oClass) {
			return $oClass->getInstance();
		} else {
			if ($defaultClassName) {
				$oClass = CodeBase::getClass($defaultClassName, false);
				if ($oClass) {
					return $oClass->getInstance();
				}
			}
		}
		return null;
	}
	
	//************************************************************************************
	public function getArray($name) {
		$val = $this->getValue($name);
		if (is_array($val)) {
			return $val;
		} else {
			return array();
		}
	}
	
	//************************************************************************************
	public function getRootArray() {
		return $this->config;
	}
	
	//************************************************************************************
	public function getTree($name='') {
		if ($name == '') return $this->config;
		
		$arr = $this->config;
		foreach(explode('.',$name) as $p) $arr = $arr[$p];
		
		return $arr;
	}
	
	//************************************************************************************
	public function setValue($name, $value) {
		if (!$name) throw new InvalidArgumentException('Name cannot be empty !');
		
		$arr = array();
		
		$str = '$arr';
		foreach(explode('.',$name) as $p) $str .= sprintf("['%s']", $p);
		$str .= ' = $value;';
		eval($str);
		
		$this->config = self::mergeConfig($this->config, $arr);
	}

	//************************************************************************************
	public function loadFromXML($fileName) {
		if (!file_exists($fileName)) return false;
		
		$oXml = new SimpleXMLElement(file_get_contents($fileName));
		foreach($oXml->config as $oNode) {
			false && $oNode = new SimpleXMLElement();
			
			$name = (string)$oNode['name'];
			$value1 = (string)$oNode['value'];
			$value2 = (string)$oNode;
			
			if ($value1) {
				$this->setValue($name, $value1);
			}
			elseif ($value2) {
				$this->setValue($name, $value2);
			}
		}
		
		return true;
	}
	
	//************************************************************************************
	public function loadFromJSON($fileName) {
		if (!file_exists($fileName)) return false;

		$content = array();
		foreach(file($fileName) as $line) {
			$line = trim($line);
			
			if (strpos($line,'//') !== false) {
				$len = strlen($line);
				$pos = -1;
				$inString = false;
				for($i=0;$i<$len;++$i) {
					if (substr($line, $i, 1) == '"') $inString = !$inString;
					if (substr($line, $i, 2) == '//' && !$inString) {
						$pos = $i;
						break;
					}
				}
				if ($pos >= 0) {
					$line = substr($line, 0, $pos);
				}
			}
			
			$content[] = $line;			
		}
		$content = implode('',$content);
		$content = preg_replace('/\,\s*\]/i',']',$content);
		$content = preg_replace('/\,\s*\}/i','}',$content);
		
		$arr = @json_decode($content, true);
		if (is_array($arr)) {
			$this->config = self::mergeConfig($this->config, $arr);
		}
		
		return true;
	}
	
	//************************************************************************************
	public function loadDirectory($path) {
		$path = rtrim($path,'/') . '/';
		if (!is_dir($path)) throw new IOException(sprintf('Path %s is not directory', $path));
		
		$oIterator = new DirectoryIterator($path);
		foreach($oIterator as $oFile) {
			false && $oFile = new SplFileInfo();
			if ($oFile->getExtension() == 'xml') $this->loadFromXML($oFile->getPathname());
			if ($oFile->getExtension() == 'json') $this->loadFromJSON($oFile->getPathname());
		}
	}
	
	//************************************************************************************
	public function loadDirectoryWithVariant($path, $variant) {
		$path = rtrim($path,'/') . '/';
		$variant = trim($variant);
		if (!is_dir($path)) throw new IOException(sprintf('Path %s is not directory', $path));
		if (!$variant) throw new InvalidArgumentException('Empty variant');
		if (strpos($variant, '/') !== false) throw new InvalidArgumentException('Invalid variant name format');
		if (strpos($variant, '.') !== false) throw new InvalidArgumentException('Invalid variant name format');
		
		if (is_dir($path . $variant)) {
			// ok, to sobie ustalamy co zaladowac
			
			$filesToLoad = array();
			
			if (true) {
				$oIterator = new DirectoryIterator($path);
				foreach($oIterator as $oFile) {
					false && $oFile = new SplFileInfo();
					if ($oFile->getExtension() == 'xml' || $oFile->getExtension() == 'json') {
						$filesToLoad[basename($oFile->getFilename())] = $oFile->getPathname();
					}
				}
			}
			if (true) {
				$oIterator = new DirectoryIterator($path . $variant);
				foreach($oIterator as $oFile) {
					false && $oFile = new SplFileInfo();
					if ($oFile->getExtension() == 'xml' || $oFile->getExtension() == 'json') {
						$filesToLoad[basename($oFile->getFilename())] = $oFile->getPathname();
					}
				}
			}
			
			foreach($filesToLoad as $path) {
				if (substr($path,-4) == '.xml') {
					$this->loadFromXML($path);
				}
				if (substr($path,-5) == '.json') {
					$this->loadFromJSON($path);
				}
			}
			
		} else {
			$this->loadDirectory($path);
		}
	}
	
	//************************************************************************************
	private static function mergeConfig($current, $arr) {
		if (!is_array($current)) throw new InvalidArgumentException('current is not array');
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not array');
		
		foreach($arr as $k => $v) {
			if (isset($current[$k])) {
				if (is_array($current[$k])) {
					if (!is_array($arr[$k])) {
						throw new ConfigurationException(sprintf('Merging failed (entry %s). Trying to replace array with primitive', $k));
					}
					$current[$k] = self::mergeConfig($current[$k], $arr[$k]);
				} else {
					$current[$k] = $v;
				}
			} else {
				$current[$k] = $v;
			}
		}
		
		return $current;
	}
	
}

?>