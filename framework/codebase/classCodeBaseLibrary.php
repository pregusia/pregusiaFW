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


class CodeBaseLibrary {
	
	private $name;
	private $version;
	private $priority;
	private $path;
	private $enabled;
	
	/**
	 * @var CodeBaseDeclaredType[]
	 */
	private $types = array();
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function getVersion() { return $this->version; }
	public function getPriority() { return $this->priority; }
	public function getPath() { return $this->path; }
	public function isEnabled() { return $this->enabled; }
	
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @throws CodeBaseException
	 * @return CodeBaseLibrary
	 */
	public static function LoadPhar($path) {
		if (substr($path,-5) != '.phar') throw new CodeBaseException(sprintf('Path %s is not phar', $path));
		return new CodeBaseLibrary($path);
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @throws CodeBaseException
	 * @return CodeBaseLibrary
	 */
	public static function LoadDirectory($path) {
		if (!is_dir($path)) throw new CodeBaseException(sprintf('Path %s is not directory', $path));
		return new CodeBaseLibrary($path);
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @throws CodeBaseException
	 * @return CodeBaseLibrary
	 */
	public static function LoadRemote($path) {
		if (substr($path,-7) != '.remote') throw new CodeBaseException(sprintf('Path %s is not remote', $path));
		
		// TODO: handling different remote types
		
		$info = json_decode(file_get_contents($path), true);
		if (!is_array($info)) throw new CodeBaseException(sprintf('Remote path %s has invalid contents', $path));
		
		if ($info['type'] == 'localfs') {
			if (!is_dir($info['path'])) throw new CodeBaseException(sprintf('Path %s is not directory', $info['path']));
			return new CodeBaseLibrary($info['path']);
		}
		else {
			throw new CodeBaseException(sprintf('Remote %s - unknown type', $path));
		}
	}
	
	//************************************************************************************
	private function throwException($msg) {
		if ($this->name) {
			throw new CodeBaseException(sprintf('In library %s - %s', $this->name, $msg));
		} else {
			throw new CodeBaseException(sprintf('In library %s - %s', $this->path, $msg));
		}
	}
	
	//************************************************************************************
	private function __construct($path) {
		if (is_dir($path)) {
			$path = rtrim($path,'/') . '/';
		}
		$this->path = $path;
		
		$info = $this->getResource('library.json')->parseJSON();
		$this->name = trim($info['name']);
		$this->version = intval($info['version']);
		$this->priority = intval($info['priority']);
		$this->enabled = true;
		
		if (isset($info['enabled'])) {
			$this->enabled = $info['enabled'] ? true : false;
		}
		
		if (!$this->name) $this->throwException('empty name');
		if ($this->version <= 0) $this->throwException('invalid version');
		
		$this->loadTypes();
	}
	
	//************************************************************************************
	private function loadTypes() {
		if ($this->exists('types.json')) {
			$arr = @json_decode($this->getResource('types.json')->contents(), true);
			if (is_array($arr)) {
				foreach($arr as $t) {
					$oType = null;
					if ($t['kind'] == 'iface') $oType = new CodeBaseDeclaredInterface($t['name'], $this->getResource($t['path']));
					if ($t['kind'] == 'class') $oType = new CodeBaseDeclaredClass($t['name'], $this->getResource($t['path']));
					if ($t['kind'] == 'trait') $oType = new CodeBaseDeclaredTrait($t['name'], $this->getResource($t['path']));
					
					if ($oType) {
						foreach($t['annos'] as $a) {
							if ($oAnno = CodeBaseAnnotation::ParseSingle($a)) {
								$oType->getAnnotations()->add($oAnno);
							}
						}
						
						$this->types[$oType->getName()] = $oType;
					}
				}
			}
		}
	}
	
	//************************************************************************************
	/**
	 * Usuwa typy ktore nie spelniaja wymagan
	 */
	public function checkRequirements() {
		$toDel = array();
		
		foreach($this->types as $key => $oType) {
			if ($oType->getNeededLibraries()) {
				// czegos wymaga, sprawdzamy czy spelnia wsio
				$ok = true;
				foreach($oType->getNeededLibraries() as $libName) {
					if (!CodeBase::hasLibrary($libName)) $ok = false;
				}
				if (!$ok) {
					$toDel[] = $key;
				}
			}
		}
		
		foreach($toDel as $key) {
			unset($this->types[$key]);
		}
	}
	
	//************************************************************************************
	public function realPath($name) {
		if (substr($this->path,-5) == '.phar') {
			return 'phar://' . $this->path . '/' . $name;
		}
		elseif (is_dir($this->path)) {
			return $this->path . $name;
		}
		else {
			throw new IOException('Invalid path - ' . $this->path);
		}
	}
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function exists($name) {
		$path = $this->realPath($name);
		return @file_exists($path);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseLibraryResource
	 */
	public function getResource($name, $throwException=true) {
		if ($this->exists($name)) {
			return new CodeBaseLibraryResource($this, $name);
		} else {
			if ($throwException) {
				throw new IOException('Resource ' . $name . ' not found in library ' . $this->name . ' (rp=' . $this->realPath($name) . ')');
			} else {
				return new CodeBaseLibraryResource(null,'');
			}
		}
	}

	//************************************************************************************
	public function loadType($name) {
		$oType = $this->getType($name);
		if (!$oType) throw new TypeNotFoundException($name, $this->name, '');
		
		$path = $oType->getResource()->realPath();
		require_once($path);
		
		if (!(class_exists($name, false) || interface_exists($name, false) || trait_exists($name, false))) {
			throw new TypeNotFoundException($name, $this->name, $oType->getResource()->getName());
		}
	}
	
// ####################################################################################
// types handling methods
// ####################################################################################

	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseDeclaredType
	 */
	public function getType($name) {
		return isset($this->types[$name]) ? $this->types[$name] : null; 
	}
	
	//************************************************************************************
	public function hasType($name) {
		return isset($this->types[$name]) ? true : false;
	}
	
	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredType[]
	 */
	public function getAllTypes() { return $this->types; }

	//************************************************************************************
	/**
	 * Zwraca klasy posiadajace podana adnotacje
	 * @param string $anno
	 * @return CodeBaseDeclaredType[]
	 */
	public function getAnnotatedTypes($anno) {
		$arr = array();
		foreach($this->types as $oType) {
			if ($oType->getAnnotations()->has($anno)) $arr[] = $oType;
		}
		return $arr;
	}
	
// ####################################################################################
// classes handling method
// ####################################################################################
	
	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredClass[]
	 */
	public function getAllClasses() {
		$arr = array();
		foreach($this->types as $oType) {
			if ($oType instanceof CodeBaseDeclaredClass) {
				$arr[] = $oType;
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	public function hasClass($name) {
		if ($oType = $this->getType($name)) {
			return $oType->isClass();
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseDeclaredClass
	 */
	public function getClass($name) {
		if ($oType = $this->getType($name)) {
			if ($oType->isClass()) {
				return $oType;
			}
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * Zwraca klasy ktore dziedzicza jakos $name 
	 * @param string $name
	 * @return CodeBaseDeclaredClass[]
	 */
	public function getClassesExtending($name) {
		$arr = array();
		foreach($this->getAllClasses() as $oClass) {
			if ($oClass->isExtending($name)) $arr[] = $oClass;
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * Zwraca klasy ktore implementuja $name 
	 * @param string $name
	 * @return CodeBaseDeclaredClass[]
	 */
	public function getClassesImplementing($name) {
		$arr = array();
		foreach($this->getAllClasses() as $oClass) {
			if ($oClass->isImplementing($name)) $arr[] = $oClass;
		}
		return $arr;
	}
	
	
// ####################################################################################
// interfaces handling method
// ####################################################################################
	
	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredInterface[]
	 */
	public function getAllInterfaces() {
		$arr = array();
		foreach($this->types as $oType) {
			if ($oType instanceof CodeBaseDeclaredInterface) {
				$arr[] = $oType;
			}
		}
		return $arr;		
	}
	
	//************************************************************************************
	public function hasInterface($name) {
		if ($oType = $this->getType($name)) {
			return $oType->isInterface();
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseDeclaredInterface
	 */
	public function getInterface($name) {
		if ($oType = $this->getType($name)) {
			if ($oType->isInterface()) {
				return $oType;
			}
		}
		return null;
	}
	
// ####################################################################################
// traits handling method
// ####################################################################################
	
	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredTrait[]
	 */
	public function getAllTraits() {
		$arr = array();
		foreach($this->types as $oType) {
			if ($oType instanceof CodeBaseDeclaredTrait) {
				$arr[] = $oType;
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	public function hasTrait($name) {
		if ($oType = $this->getType($name)) {
			return $oType->isTrait();
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseDeclaredTrait
	 */
	public function getTrait($name) {
		if ($oType = $this->getType($name)) {
			if ($oType->isTrait()) {
				return $oType;
			}
		}
		return null;
	}
	
}

?>