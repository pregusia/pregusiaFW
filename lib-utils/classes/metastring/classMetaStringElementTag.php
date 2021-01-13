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


final class MetaStringElementTag extends MetaStringElement {
	
	private static $TAGS_HANDLERS = false;
	
	//************************************************************************************
	/**
	 * @return IMetaStringTagsHandler[]
	 */
	private static function getTagsHandlers() {
		if (self::$TAGS_HANDLERS === false) {
			self::$TAGS_HANDLERS = CodeBase::getInterface('IMetaStringTagsHandler')->getAllInstances();
			usort(self::$TAGS_HANDLERS, function($a, $b){ return $b->getPriority() - $a->getPriority(); });
		}
		return self::$TAGS_HANDLERS;
	}
	
	private $tagName = '';
	private $tagParam = '';
	
	/**
	 * @var MetaStringElement
	 */
	private $oInner = null;
	
	//************************************************************************************
	public function getTagName() { return $this->tagName; }
	public function getTagParam() { return $this->tagParam; }
	
	//************************************************************************************
	/**
	 * @return MetaStringElement
	 */
	public function getInner() { return $this->oInner; }
	
	//************************************************************************************
	private function __construct($tagName, $tagParam, $oInner) {
		if (!($oInner instanceof MetaStringElement)) throw new InvalidArgumentException('oInner is not MetaStringElement');
		$this->tagName = $tagName;
		$this->tagParam = $tagParam;
		$this->oInner = $oInner;
	}
	
	//************************************************************************************
	public function render($ctx, $renderFlags) {
		$oHandler = null;
		
		if (($renderFlags & MetaStringElement::RENDER_STRIP) == 0) {
			foreach(self::getTagsHandlers() as $h) {
				if (in_array($this->getTagName(), $h->getTags())) {
					$oHandler = $h;
					break;
				}
			}
		}
		
		if ($oHandler) {
			return $oHandler->parse($ctx, $this->getTagName(), $this->getTagParam(), $this->getInner()->render($ctx, $renderFlags));
		} else {
			return $this->getInner()->render($ctx, $renderFlags);
		}
	}
	
	//************************************************************************************
	/**
	 * @param MetaStringTokensCollection $oTokens
	 * @return MetaStringElementTag
	 */
	public static function Parse($oTokens) {
		if (!($oTokens instanceof MetaStringTokensCollection)) throw new InvalidArgumentException('oTokens is not MetaStringTokensCollection');
		
		if ($oTokens->isNext(MetaStringToken::TYPE_TAG_OPEN)) {
			$oTokens->markSet();
			$oTagToken = $oTokens->popNext();
			
			$oInner = MetaStringElementList::Parse($oTokens);
			if ($oInner) {				
				if ($oTokens->isNext(MetaStringToken::TYPE_TAG_CLOSE)) {
					$oTagEndToken = $oTokens->popNext();
					if ($oTagEndToken->getTagName() == $oTagToken->getTagName()) {
						
						$oTokens->markCancel();
						return new MetaStringElementTag($oTagToken->getTagName(), $oTagToken->getTagParam(), $oInner);
					}
				}
			}
							
			$oTokens->markBack();
		}
		if ($oTokens->isNext(MetaStringToken::TYPE_TAG_SINGLE)) {
			$oTagToken = $oTokens->popNext();
			return new MetaStringElementTag($oTagToken->getTagName(), $oTagToken->getTagParam(), new MetaStringElementRaw(''));
		}
		
		return null;
	}
	
}

?>