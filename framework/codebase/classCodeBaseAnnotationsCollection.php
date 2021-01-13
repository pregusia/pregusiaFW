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


class CodeBaseAnnotationsCollection implements IteratorAggregate {
	
	/**
	 * @var CodeBaseAnnotation[]
	 */
	private $items = array();
	
	//************************************************************************************
	public function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * @param CodeBaseAnnotation $oAnno
	 * @throws InvalidArgumentException
	 */
	public function add($oAnno) {
		if (!($oAnno instanceof CodeBaseAnnotation)) throw new InvalidArgumentException('oAnno is not CodeBaseAnnotation');
		$this->items[] = $oAnno;
	}
	
	//************************************************************************************
	/**
	 * @return CodeBaseAnnotation[]
	 */
	public function getItems() {
		return $this->items;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return CodeBaseAnnotation
	 */
	public function getFirst($name) {
		foreach($this->items as $oAnno) {
			if ($oAnno->getName() == $name) return $oAnno;
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * Uzywajac @param zwraca typ parametru
	 * @param string $paramName
	 */
	public function getParameterType($paramName) {
		$paramName = trim($paramName,'$');
		foreach($this->getAll('param') as $oAnno) {
			if (trim($oAnno->getParam(1),'$') == $paramName) {
				return $oAnno->getParam(0);
			}
		}
		return '';
	}
	
	//************************************************************************************
	/**
	 * @param unknown $name
	 * @return CodeBaseAnnotation[]
	 */
	public function getAll($name) {
		if (!$name) return $this->getItems();
		
		$arr = array();
		
		foreach($this->items as $oAnno) {
			if ($oAnno->getName() == $name) $arr[] = $oAnno;
		}
		
		return $arr;
	}
	
	//************************************************************************************
	public function has($name) {
		foreach($this->items as $oAnno) {
			if ($oAnno->getName() == $name) return true;
		}
		return false;
	}
	
	//************************************************************************************
	public function getIterator() {
		return new ArrayIterator($this->items);
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @return CodeBaseAnnotationsCollection
	 */
	public static function ParseDocComment($str) {
		$oCol = new CodeBaseAnnotationsCollection();
		
		foreach(explode("\n",$str) as $line) {
			$line = trim($line,"\t *");
			if (!$line) continue;
			if (substr($line,0,1) == '@') {
				$oAnno = CodeBaseAnnotation::ParseSingle(substr($line, 1));
				if ($oAnno) {
					$oCol->items[] = $oAnno;
				}
			}
		}
		
		return $oCol;
	}
	
}

?>