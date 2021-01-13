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

abstract class APIPaginatedResults implements JsonSerializable {
	
	protected $total = 0;
	protected $offset = 0;
	protected $limit = 0;
	
	/**
	 * @var JsonSerializable[]
	 */
	protected $items = array();
	
	
	//************************************************************************************
	protected abstract function getItemClassName();

	//************************************************************************************
	public function getTotal() { return $this->total; }
	public function setTotal($v) { $this->total = $v; }
	
	//************************************************************************************
	public function getOffset() { return $this->offset; }
	public function setOffset($v) { $this->offset = $v; }
	
	//************************************************************************************
	public function getLimit() { return $this->limit; }
	public function setLimit($v) { $this->limit = $v; }
	
	//************************************************************************************
	/**
	 * @return JsonSerializable[]
	 */
	public function getItems() { return $this->items; }
	
	//************************************************************************************
	public function clearItems() { $this->items = array(); }

	//************************************************************************************
	/**
	 * @param JsonSerializable $e
	 */
	public function addItem($e) {
		if (!$e) throw new InvalidArgumentException('Trying to add null element');
		if (!($e instanceof JsonSerializable)) throw new InvalidArgumentException('e is not JsonSerializable');
		$this->items[] = $e;
	}
	

	//************************************************************************************
	public function __construct() {
		
	}

	//************************************************************************************
	public function jsonSerialize() {
		$arr = array(
			'total' => $this->total,
			'offset' => $this->offset,
			'limit' => $this->limit,
			'items' => array()	
		);
		foreach($this->items as $oItem) {
			$arr['items'][] = $oItem->jsonSerialize();
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return self
	 */
	public static function jsonUnserialize($arr) {
		$oClass = CodeBase::getClass(get_called_class());
		$obj = $oClass->ctorCreate();
		$oItemClass = CodeBase::getClass($obj->getItemClassName());
		
		$obj->total = intval($arr['total']);
		$obj->offset = intval($arr['offset']);
		$obj->limit = intval($arr['limit']);
		
		if (is_array($arr['items'])) {
			foreach($arr['items'] as $e) {
				$oItem = $oItemClass->callStaticMethod('jsonUnserialize', array($e));
				if ($oItem) {
					$obj->addItem($oItem);
				}
			}
		}
		
		return $obj;
	}
	
	
	
}

?>