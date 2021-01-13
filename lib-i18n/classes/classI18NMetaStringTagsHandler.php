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


class I18NMetaStringTagsHandler implements IMetaStringTagsHandler {
	
	public function getTags() { return array('i18n'); }
	public function getPriority() { return 10; }

	//************************************************************************************
	/**
	 * @return I18NApplicationComponent
	 */
	private function getI18NComponent() {
		return ApplicationContext::getCurrent()->getComponent('i18n');
	}
	
	//************************************************************************************
	/**
	 * @param object $ctx
	 * @param string $tagName
	 * @param string $tagParam
	 * @param string $innerText
	 */
	public function parse($ctx, $tagName, $tagParam, $innerText) {
		if ($tagName == 'i18n') {
			$key = $tagParam ? $tagParam : $innerText;
			$value = $this->getI18NComponent()->translate($key);
			
			if ($value == $key) {
				if ($innerText) {
					return $innerText;
				} else {
					return $value;
				}
			}
			
			
			// zwrocona wartosc moze zawierac ponownie meta-tagi, wiec trzeba je przeparsowac
			if (strpos($value,'[') !== false) {
				$oTokens = MetaStringTokensCollection::Tokenize($value);
				$oElement = MetaStringElementList::Parse($oTokens);
				if ($oElement) {
					$value = $oElement->render($ctx, 0);
				}
			} 
			
			return $value;
		}
		return $innerText;
	}
	
}

?>