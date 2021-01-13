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


class GetTextMOReader {
	
	const MAGIC1 = -1794895138;
	const MAGIC2 = -569244523;
	const MAGIC3 = 2500072158;
	
	/**
	 * @var StringReader
	 */
	private $oReader = null;
	
	private $byteOrder = 'V';
	
	//************************************************************************************
	/**
	 * @return StringReader
	 */
	public function getReader() { return $this->oReader; }
	
	
	//************************************************************************************
	public function __construct($contents) {
		$this->oReader = new StringReader($contents);
		
		$magic = $this->readInt();
		if (($magic === self::MAGIC1) || ($magic === self::MAGIC3)) { //to make sure it works for 64-bit platforms
			$this->byteOrder = 'V'; //low endian
		} elseif ($magic === (self::MAGIC2 & 0xFFFFFFFF)) {
			$this->byteOrder = 'N'; //big endian
		} else {
			throw new IOException('Not MO file');
		}
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function readInt() {
		if (($read = $this->getReader()->read(4)) === false) return false;
		$read = unpack($this->byteOrder, $read);
		return intval(array_shift($read));
	}
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function readIntArray($count) {
		return unpack($this->byteOrder . $count, $this->getReader()->read(4 * $count));
	}
	
	//************************************************************************************
	public function read($bytes) {
		return $this->getReader()->read($bytes);
	}
	
	//************************************************************************************
	public function seekTo($pos) {
		$this->getReader()->seekto($pos);
	}
	
}

?>