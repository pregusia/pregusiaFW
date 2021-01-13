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


final class MetaStringElementList extends MetaStringElement {
	
	/**
	 * @var MetaStringElement[]
	 */
	private $items = array();
	
	
	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	public function render($ctx, $renderFlags) {
		$str = '';
		foreach($this->items as $oElement) {
			$str .= $oElement->render($ctx, $renderFlags);
		}
		return $str;
	}
	
	//************************************************************************************
	/**
	 * @param MetaStringTokensCollection $oTokens
	 * @return MetaStringElementList
	 */
	public static function Parse($oTokens) {
		if (!($oTokens instanceof MetaStringTokensCollection)) throw new InvalidArgumentException('oTokens is not MetaStringTokensCollection');
		
		$obj = new MetaStringElementList();

		while(!$oTokens->isNext(MetaStringToken::TYPE_NONE)) {
			
			if ($oTokens->isNext(MetaStringToken::TYPE_RAW)) {
				$oToken = $oTokens->popNext();
				$obj->items[] = new MetaStringElementRaw($oToken->getText());
				continue;
			}
			
			if ($oTokens->isNext(MetaStringToken::TYPE_TAG_OPEN) || $oTokens->isNext(MetaStringToken::TYPE_TAG_SINGLE)) {
				$oElement = MetaStringElementTag::Parse($oTokens);
				if ($oElement) {
					$obj->items[] = $oElement;
				} else {
					$oToken = $oTokens->popNext();
					$obj->items[] = new MetaStringElementRaw($oToken->getRawText());
				}
				continue;
			}
			
			break;
		}
		
		return $obj;
	}
	
	
}

?>