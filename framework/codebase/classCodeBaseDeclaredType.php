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


abstract class CodeBaseDeclaredType {

	private $name = '';
	
	/**
	 * @var CodeBaseLibraryResource
	 */
	private $oResource = null;
	
	/**
	 * @var CodeBaseAnnotationsCollection
	 */
	private $oAnnotations = null;
	
	//************************************************************************************
	public function getName() { return $this->name; }

	//************************************************************************************
	/**
	 * @return CodeBaseLibraryResource
	 */
	public function getResource() { return $this->oResource; }
	
	//************************************************************************************
	/**
	 * @return CodeBaseAnnotationsCollection
	 */
	public function getAnnotations() { return $this->oAnnotations; }

	//************************************************************************************
	/**
	 * @param string $name
	 * @param CodeBaseLibraryResource $oResource
	 * @param string $annos
	 */
	public function __construct($name, $oResource) {
		if (!($oResource instanceof CodeBaseLibraryResource)) throw new InvalidArgumentException('oResource is not CodeBaseLibraryResource');
		if (!$oResource->exists()) throw new InvalidArgumentException('Given resource not exists');
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Name is empty');
		
		$this->name = $name;
		$this->oResource = $oResource;
		$this->oAnnotations = new CodeBaseAnnotationsCollection();
	}
	
	//************************************************************************************
	/**
	 * @return string[]
	 */
	public function getNeededLibraries() {
		$arr = array();
		foreach($this->getAnnotations()->getAll('NeedLibrary') as $oAnno) {
			$libName = trim($oAnno->getParam(0));
			if ($libName) {
				$arr[] = $libName;
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	public abstract function isClass();
	public abstract function isInterface();
	public abstract function isTrait();
	
}

?>