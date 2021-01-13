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

class Spatial2DRect implements JsonSerializable {
	
	private $point = null;
	private $size = null;
	
	//************************************************************************************
	/**
	 * @return Spatial2DPoint
	 */
	public function getPoint() { return $this->point; }
	
	//************************************************************************************
	/**
	 * @return Spatial2DSize
	 */
	public function getSize() { return $this->size; }
	
	
	//************************************************************************************
	public function getX() { return $this->getPoint()->getX(); }
	public function getY() { return $this->getPoint()->getY(); }
	public function getWidth() { return $this->getSize()->getWidth(); }
	public function getHeight() { return $this->getSize()->getHeight(); }
	
	//************************************************************************************
	public function getLeft() { return $this->getX(); }
	public function getRight() { return $this->getX() + $this->getWidth(); }
	public function getTop() { return $this->getY(); }
	public function getBottom() { return $this->getY() + $this->getHeight(); }
	
	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * @param Spatial2DPoint $oPoint
	 * @return bool
	 */
	public function contains($oPoint) {
		if (!($oPoint instanceof Spatial2DPoint)) throw new InvalidArgumentException('oPoint is not Spatial2DPoint');
		return $oPoint->getX() >= $this->getLeft() && $oPoint->getX() < $this->getRight()
			&& $oPoint->getY() >= $this->getTop() && $oPoint->getY() < $this->getBottom();
	}
	
	//************************************************************************************
	/**
	 * @return Spatial2DPoint[]
	 */
	public function enumerateIntPoints() {
		$arr = array();
		for($x=intval($this->getLeft());$x < intval($this->getRight()); ++$x) {
			for($y=intval($this->getTop());$y < intval($this->getBottom()); ++$y) {
				$arr[] = new Spatial2DPoint($x, $y);
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			"point" => $this->point->jsonSerialize(),
			"size" => $this->size->jsonSerialize(),
		);
	}

	//************************************************************************************
	/**
	 * @param array $arr
	 * @return self
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["point"] && $arr['size']) {
			$oPoint = Spatial2DPoint::jsonUnserialize($arr["point"]);
			$oSize = Spatial2DSize::jsonUnserialize($arr["size"]);
			
			if ($oPoint && $oSize) {
				return self::CreatePointSize($oPoint, $oSize);
			}
		}
		return null;
	}
	

	//************************************************************************************
	/**
	 * @param Spatial2DPoint $oPoint
	 * @param Spatial2DSize $oSize
	 * @return Spatial2DRect
	 */
	public static function CreatePointSize($oPoint, $oSize) {
		if (!($oPoint instanceof Spatial2DPoint)) throw new InvalidArgumentException('oPoint is not Spatial2DPoint');
		if (!($oSize instanceof Spatial2DSize)) throw new InvalidArgumentException('oSize is not Spatial2DSize');
		$obj = new self();
		$obj->point = $oPoint;
		$obj->size = $oSize;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param float $x
	 * @param float $y
	 * @param float $width
	 * @param float $height
	 * @return Spatial2DRect
	 */
	public static function CreateRect($x, $y, $width, $height) {
		$obj = new self();
		$obj->point = new Spatial2DPoint($x, $y);
		$obj->size = new Spatial2DSize($width, $height);
		return $obj;
	}
	
}


?>