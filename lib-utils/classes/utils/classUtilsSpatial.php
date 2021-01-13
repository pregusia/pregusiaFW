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

class UtilsSpatial {
	
	//************************************************************************************
	/**
	 * @param Spatial2DPoint $oPoint
	 * @param Spatial2DPoint[] $polygon
	 * @return bool
	 */
	public static function isPointInPolygon2D($oPoint, $polygon) {
		if (!($oPoint instanceof Spatial2DPoint)) throw new InvalidArgumentException('oPoint is not Spatial2DPoint');
		UtilsArray::checkArgument($polygon, 'Spatial2DPoint');
		
		if (count($polygon) == 0) return false;
		
		$inside = false;
		for($i=0,$j=count($polygon) - 1; $i < count($polygon); $j = $i++) {
			
			if ( ($polygon[$i]->getY() > $oPoint->getY()) != ($polygon[$j]->getY() > $oPoint->getY()) ) {
				
				if ($oPoint->getX() < ($polygon[$j]->getX() - $polygon[$i]->getX()) * ($oPoint->getY() - $polygon[$i]->getY()) / ($polygon[$j]->getY() - $polygon[$i]->getY()) + $polygon[$i]->getX()) {
					$inside = !$inside;
				}
			}
			
		}
		
		return $inside;
	}
	
	
	//************************************************************************************
	/**
	 * @param SpatialLatLng $oPoint
	 * @param SpatialLatLng[] $polygon
	 * @return bool
	 */
	public static function isPointInPolygonLatLng($oPoint, $polygon) {
		if (!($oPoint instanceof SpatialLatLng)) throw new InvalidArgumentException('oPoint is not SpatialLatLng');
		UtilsArray::checkArgument($polygon, 'SpatialLatLng');
		
		if (count($polygon) == 0) return false;
		
		$inside = false;
		for($i=0,$j=count($polygon) - 1; $i < count($polygon); $j = $i++) {
			
			if ( ($polygon[$i]->getLng() > $oPoint->getLng()) != ($polygon[$j]->getLng() > $oPoint->getLng()) ) {
				
				if ($oPoint->getLat() < ($polygon[$j]->getLat() - $polygon[$i]->getLat()) * ($oPoint->getLng() - $polygon[$i]->getLng()) / ($polygon[$j]->getLng() - $polygon[$i]->getLng()) + $polygon[$i]->getLat()) {
					$inside = !$inside;
				}
			}
			
		}
		
		return $inside;
	}	
	
}


?>