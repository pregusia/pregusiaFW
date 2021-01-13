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


class StringReader {
	
	public $pos;
	public $str;
	public $strlen;

	//************************************************************************************
	public function __construct($str) {
		$this->str = $str;
		$this->strlen = strlen($this->str);
	}

	//************************************************************************************
	public function read($bytes) {
		$data = substr($this->str, $this->pos, $bytes);
		$this->seekto($this->pos + $bytes);
		return $data;
	}

	//************************************************************************************
	public function seekto($pos) {
		$this->pos = ($this->strlen < $pos) ? $this->strlen : $pos;
		return $this->pos;
	}
}

?>