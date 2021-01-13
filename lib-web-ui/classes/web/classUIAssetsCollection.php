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


class UIAssetsCollection {
	
	/**
	 * @var string[]
	 */
	private $cssLocations = array();
	
	/**
	 * @var string[]
	 */
	private $jsLocations = array();
	
	
	//************************************************************************************
	public function addJSLocation($loc, $first=false) {
		if ($first) {
			array_unshift($this->jsLocations, $loc);
		} else {
			$this->jsLocations[] = $loc;
		}
	}
	
	//************************************************************************************
	public function addCSSLocation($loc, $first=false) {
		if ($first) {
			array_unshift($this->cssLocations, $loc);
		} else {
			$this->cssLocations[] = $loc;
		}
	}
	
	//************************************************************************************
	public function removeJSLocation($loc) {
		$arr = array();
		foreach($this->jsLocations as $l) {
			if ($l != $loc) $arr[] = $l;
		}
		$this->jsLocations = $arr;
	}
	
	//************************************************************************************
	public function removeCSSLocation($loc) {
		$arr = array();
		foreach($this->cssLocations as $l) {
			if ($l != $loc) $arr[] = $l;
		}
		$this->cssLocations = $arr;
	}
	
	//************************************************************************************
	public function replaceJSLocation($oldLoc, $newLoc) {
		$arr = array();
		foreach($this->jsLocations as $l) {
			if ($l == $oldLoc) {
				$arr[] = $newLoc;
			} else {
				$arr[] = $l;
			}
		}
		$this->jsLocations = $arr;		
	}
	
	//************************************************************************************
	public function replaceCSSLocation($oldLoc, $newLoc) {
		$arr = array();
		foreach($this->cssLocations as $l) {
			if ($l == $oldLoc) {
				$arr[] = $newLoc;
			} else {
				$arr[] = $l;
			}
		}
		$this->cssLocations = $arr;		
	}
	
	//************************************************************************************
	public function getJSContent() {
		$content = array();
		foreach($this->jsLocations as $loc) {
			$content[] = sprintf('// %s', $loc);
			$content[] = '(function(){';
			$content[] = CodeBase::getResource($loc,false)->contents();
			$content[] = '}).apply(window);';
			$content[] = '';
			$content[] = '';
		}
		
		return implode("\n",$content);
	}
	
	//************************************************************************************
	public function getCSSContent() {
		$content = array();
		foreach($this->cssLocations as $loc) {
			$content[] = sprintf('/* %s */', $loc);
			$content[] = CodeBase::getResource($loc,false)->contents();
			$content[] = '';
			$content[] = '';
		}
		
		return implode("\n",$content);
	}
}

?>