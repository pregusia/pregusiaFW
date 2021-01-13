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
 * Zawiera informacje o calym repozytorium kodu
 * zwlaszcza bibliotekach 
 * @author pregusia
 *
 */
class CodeBase {
	
	/**
	 * @var CodeBaseLibrary[]
	 */
	private static $libraries = array();
	
	/**
	 * @var IClassInstantinatorAdapter[]
	 */
	private static $instantinationAdapters = array();
	
	//************************************************************************************
	public static function Sort() {
		uasort(self::$libraries, function($a,$b){
			return $a->getPriority() - $b->getPriority();
		});
	}
	
	//************************************************************************************
	public static function CheckRequirements() {
		foreach(self::$libraries as $oLibrary) {
			$oLibrary->checkRequirements();
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @throws CodeBaseException
	 * @return CodeBaseLibrary
	 */
	public static function LoadLibrary($path) {
		$oLibrary = null;
		
		if (substr($path,-5) == '.phar') $oLibrary = CodeBaseLibrary::LoadPhar($path);
		elseif (substr($path,-7) == '.remote') $oLibrary = CodeBaseLibrary::LoadRemote($path);
		elseif (is_dir($path)) $oLibrary = CodeBaseLibrary::LoadDirectory($path);
		
		if (!$oLibrary) {
			throw new CodeBaseException(sprintf('Path %s could not be loaded as library', $path));
		}
		
		if (!$oLibrary->isEnabled()) return null;
		self::$libraries[$oLibrary->getName()] = $oLibrary;
		return $oLibrary;
	}
	
	//************************************************************************************
	/**
	 * @return CodeBaseLibrary[]
	 */
	public static function getLibraries() {
		return self::$libraries;
	}
	
	//************************************************************************************
	/**
	 * @param string $libName
	 * @return boolean
	 */
	public static function hasLibrary($libName) {
		return self::$libraries[$libName] ? true : false;
	}
	
	//************************************************************************************
	public static function ensureLibrary($libName, $neededBy) {
		if (!self::hasLibrary($libName)) {
			throw new RequirementException(sprintf('Library %s, required by %s, could not be found', $libName, $neededBy));
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $libName
	 * @return CodeBaseLibrary
	 */
	public static function getLibrary($libName, $throwException=true) {
		if (self::$libraries[$libName]) {
			return self::$libraries[$libName];
		} else {
			if ($throwException) {
				throw new CodeBaseException('Library ' . $libName . ' not found');
			} else {
				return null;
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $loc
	 * @return CodeBaseLibraryResource
	 */
	public static function getResource($loc, $throwException=true) {
		list($libName, $name) = explode(':',$loc,2);
		
		if ($libName == '*') {
			// search all libraries
			
			foreach(self::$libraries as $oLibrary) {
				if ($oLibrary->exists($name)) {
					return $oLibrary->getResource($name, $throwException);
				}
			}
			
			if ($throwException) {
				throw new IOException('Resource ' . $name . ' not found in any library (rp=' . $this->realPath($name) . ')');
			} else {
				return new CodeBaseLibraryResource(null,'');
			}
			
		} else {
			$oLibrary = self::getLibrary($libName, $throwException);
			if ($oLibrary) {
				return $oLibrary->getResource($name, $throwException);
			} else {
				return new CodeBaseLibraryResource(null, '');
			}
		}
	}
	
	//************************************************************************************
	public static function LoadType($typeName, $throwException=true) {
		foreach(self::$libraries as $oLibrary) {
			if ($oLibrary->hasType($typeName)) {
				$oLibrary->loadType($typeName);
				return true;
			}
		}
		
		if ($throwException) {
			throw new TypeNotFoundException($typeName, '', '');
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * Zwraca typy posiadajace podana adnotacje
	 * @param string $anno
	 * @return CodeBaseDeclaredType[]
	 */
	public static function getAnnotatedTypes($anno) {
		$arr = array();
		foreach(self::$libraries as $oLibrary) {
			$arr = array_merge($arr, $oLibrary->getAnnotatedTypes($anno));
		}
		return $arr;
	}
	
// ################################################################################
// classes
// ################################################################################	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseDeclaredClass
	 */
	public static function getClass($name, $throwException=true) {
		foreach(self::$libraries as $oLibrary) {
			if ($oLibrary->hasClass($name)) return $oLibrary->getClass($name);
		}
		
		if ($throwException) {
			throw new TypeNotFoundException($name, '', '');
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	public static function hasClass($name) {
		foreach(self::$libraries as $oLibrary) {
			if ($oLibrary->hasClass($name)) return true;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * Zwraca klasy ktore dziedzicza jakos $name 
	 * @param string $name
	 * @return CodeBaseDeclaredClass[]
	 */
	public static function getClassesExtending($name) {
		$arr = array();
		foreach(self::$libraries as $oLibrary) {
			$arr = array_merge($arr, $oLibrary->getClassesExtending($name));
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * Zwraca klasy ktore implementuja $name 
	 * @param string $name
	 * @return CodeBaseDeclaredClass[]
	 */
	public static function getClassesImplementing($name) {
		$arr = array();
		foreach(self::$libraries as $oLibrary) {
			$arr = array_merge($arr, $oLibrary->getClassesImplementing($name));
		}
		return $arr;
	}
	
// ################################################################################
// interfaces
// ################################################################################

	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseDeclaredInterface
	 */
	public static function getInterface($name, $throwException=true) {
		foreach(self::$libraries as $oLibrary) {
			if ($oLibrary->hasInterface($name)) return $oLibrary->getInterface($name);
		}
		
		if ($throwException) {
			throw new TypeNotFoundException($name, '', '');
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	public static function hasInterface($name) {
		foreach(self::$libraries as $oLibrary) {
			if ($oLibrary->hasInterface($name)) return true;
		}
		return false;
	}
	
// ################################################################################
// traits
// ################################################################################
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseDeclaredTrait
	 */
	public static function getTrait($name, $throwException=true) {
		foreach(self::$libraries as $oLibrary) {
			if ($oLibrary->hasTrait($name)) return $oLibrary->getTrait($name);
		}
		
		if ($throwException) {
			throw new TypeNotFoundException($name, '', '');
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	public static function hasTrait($name) {
		foreach(self::$libraries as $oLibrary) {
			if ($oLibrary->hasTrait($name)) return true;
		}
		return false;
	}
	
// ################################################################################
// instantination adapters
// ################################################################################
	
	//************************************************************************************
	/**
	 * @param IClassInstantinatorAdapter $oAdapter
	 */
	public static function registerInstantinatorAdapter($oAdapter) {
		if (!($oAdapter instanceof IClassInstantinatorAdapter)) throw new InvalidArgumentException('oAdapter is not IClassInstantinatorAdapter');
		self::$instantinationAdapters[] = $oAdapter;
	}
	
	//************************************************************************************
	/**
	 * @return IClassInstantinatorAdapter[]
	 */
	public static function getInstantinatorAdapters() {
		return self::$instantinationAdapters;
	}
	
}

?>