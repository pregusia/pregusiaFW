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

class RemoteTablePagination implements JsonSerializable {
	
	private $page = 1;
	private $perPage = 20;
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPage() { return $this->page; }
	
	//************************************************************************************
	/**
	 * @param int $v
	 */
	public function setPage($v) {
		$v = intval($v);
		if ($v < 1) $v = 1;
		$this->page = $v;
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPerPage() { return $this->perPage; }
	
	//************************************************************************************
	/**
	 * @param int $v
	 */
	public function setPerPage($v) {
		$v = intval($v);
		if ($v < 10) $v = 10;
		$this->perPage = $v;
	}

	//************************************************************************************
	public function __construct($page, $perPage) {
		$this->setPage($page);
		$this->setPerPage($perPage);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			"page" => $this->page,
			"perPage" => $this->perPage,
		);
	}
	
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return self
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["page"]) {
			return new self($arr["page"], $arr["perPage"]);
		}
		return null;
	}
	
	
}

?>