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


class TypeNotFoundException extends CodeBaseException {
	
	private $typeName = '';
	private $libraryName = '';
	private $resPath = '';
	
	//************************************************************************************
	public function getTypeName() { return $this->typeName; }
	public function getLibraryName() { return $this->libraryName; }
	public function getResourcePath() { return $this->resPath; }
	
	//************************************************************************************
	public function infoTypeName() { return $this->typeName; }
	public function infoLibraryName() { return $this->libraryName; }
	public function infoResourcePath() { return $this->resPath; }
	
	
	//************************************************************************************
	public function __construct($typeName='', $libraryName='', $resPath='') {
		$this->typeName = $typeName;
		$this->libraryName = $libraryName;
		$this->resPath = $resPath;
		
		if (true) {
			$msg = sprintf('Type "%s" not found', $typeName);
			if ($libraryName) {
				$msg .= sprintf(' in library "%s".', $libraryName);
			} else {
				$msg .= '.';
			}
			if ($resPath) {
				$msg .= sprintf(' Tried resource "%s".', $resPath);
			}
		}
		
		parent::__construct($msg);
	}
	
}

?>