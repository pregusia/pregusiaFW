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


class RemoteTableResults implements JsonSerializable {

	private $itemClassName = "";
	private $page = 0;
	private $pagesCount = 0;
	private $itemsCount = 0;
	private $items = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getItemClassName() { return $this->itemClassName; }
	
	//************************************************************************************
	/**
	 * @return ReflectionClass
	 */
	public function getItemClass() {
		if (!$this->itemClassName) throw new IllegalStateException('itemClassName not set');
		$oClass = CodeBase::getClass($this->itemClassName, true);
		if ($oClass->isAbstract()) throw new IllegalStateException(sprintf('Class %s is abstract', $this->itemClassName));
		if (!$oClass->isImplementing('JsonSerializable')) throw new IllegalStateException(sprintf('Class %s is not implementing JsonSerializable', $this->itemClassName));
		
		return $oClass->getReflectionType();
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPage() { return $this->page; }
	public function setPage($v) {
		$v = intval($v);
		if ($v < 1) $v = 1;
		$this->page = $v;
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPagesCount() { return $this->pagesCount; }
	public function setPagesCount($v) {
		$v = intval($v);
		if ($v < 0) $v = 0;
		$this->pagesCount = $v;
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getItemsCount() { return $this->itemsCount; }
	public function setItemsCount($v) {
		$v = intval($v);
		if ($v < 0) $v = 0;
		$this->itemsCount = $v;
	}
	
	//************************************************************************************
	/**
	 * @return array
	 */
	public function getItems() { return $this->items; }
	public function clearItems() { $this->items = array(); }
	
	//************************************************************************************
	/**
	 * @param object $oItem
	 */
	public function addItem($oItem) {
		if (!$oItem) throw new InvalidArgumentException('Empty item');
		$oClass = $this->getItemClass();
		if (!$oClass->isInstance($oItem)) throw new InvalidArgumentException(sprintf('oItem is not %s', $oClass->getName()));
		$this->items[] = $oItem;
	}
	
	//************************************************************************************
	/**
	 * @param string $itemClassName
	 */
	public function __construct($itemClassName) {
		$this->itemClassName = $itemClassName;
		$this->getItemClass();
		$this->page = 1;
		$this->pagesCount = 0;
		$this->itemsCount = 0;
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		$arr = array(
			"itemClassName" => $this->itemClassName,
			"page" => $this->page,
			"pagesCount" => $this->pagesCount,
			"itemsCount" => $this->itemsCount,
			"items" => array()
		);
		
		foreach($this->items as $oItem) {
			$arr['items'][] = $oItem->jsonSerialize();
		}
		
		return $arr;
	}
	
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return RemoteTableResults
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["itemClassName"] && $arr['page']) {
			$obj = new self(strval($arr["itemClassName"]));
	
			$obj->setPage($arr['page']);
			$obj->setItemsCount($arr['itemsCount']);
			$obj->setPagesCount($arr['pagesCount']);
			
			$oClass = $obj->getItemClass();
			$oMethod = $oClass->getMethod('jsonUnserialize');
			if (!$oMethod) throw new IllegalStateException(sprintf('%s::jsonUnserialize not exists', $oClass->getName()));
			if (!$oMethod->isStatic()) throw new IllegalStateException(sprintf('%s::jsonUnserialize is not static', $oClass->getName()));
			
			foreach($arr['items'] as $e) {
				$oItem = $oMethod->invoke(null, $e);
				if ($oItem) {
					$obj->items[] = $oItem;
				}
			}
			
			return $obj;
		}
		return null;
	}
	
	
	
}

?>