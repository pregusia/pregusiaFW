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


class UISelectOptionsRenderer {
	
	const FLAG_SELECTED = 1;
	const FLAG_DISABLED = 2;
	
	/**
	 * @var Enum
	 */
	private $oEnum = null;
	
	private $neutralValue = '';
	
	/**
	 * @var ComplexString
	 */
	private $neutralCaption = null;
	
	//************************************************************************************
	public function __construct($oEnum) {
		if (!($oEnum instanceof Enum)) throw new InvalidArgumentException('oEnum is not Enum');
		$this->oEnum = $oEnum;
		$this->neutralCaption = ComplexString::Adapt('---');
	}
	
	//************************************************************************************
	public function getEnum() { return $this->oEnum; }

	//************************************************************************************
	public function getNeutralValue() { return $this->neutralValue; }
	public function setNeutralValue($v) { $this->neutralValue = $v; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getNeutralCaption() { return $this->neutralCaption; }
	public function setNeutralCaption($v) { $this->neutralCaption = ComplexString::Adapt($v); }
	
	//************************************************************************************
	public function render($selected, $withNeutral, $ctx) {
		$isSelected = function($v) use (&$selected) {
			if (is_array($selected)) {
				return in_array($v, $selected);
			} else {
				return ($v == $selected);
			}
		};
		
		$str = '';
		if ($withNeutral) {
			$ok = true;
			foreach($this->getEnum()->getItems() as $value => $caption) {
				if ($value == $this->neutralValue) $ok = false;
			}
			if ($ok) {
				$flags = 0;
				if ($isSelected($this->neutralValue)) $flags |= self::FLAG_SELECTED;
				$str .= self::renderOptionTag($this->neutralValue, $this->neutralCaption, $flags, $ctx);
			}
		}
		foreach($this->getEnum()->getItems() as $value => $caption) {
			$flags = 0;
			
			if ($caption->hasMetaTag('ui.option.disabled')) {
				$flags |= self::FLAG_DISABLED;
			}
			elseif ($isSelected($value)) {
				$flags |= self::FLAG_SELECTED;
			}
			
			$str .= self::renderOptionTag($value, $caption, $flags, $ctx);
		}
		return $str;
	}
	
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param ComplexString $caption
	 * @param int $flags
	 * @return string
	 */
	public static function renderOptionTag($value,$caption,$flags, $ctx) {
		$caption = ComplexString::Adapt($caption);
		
		if ($flags & self::FLAG_DISABLED) {
			return sprintf('<option disabled="disabled" style="font-weight: bold;" data-html="%s">%s</option>',
				htmlspecialchars($caption->render($ctx)),
				$caption->render($ctx, MetaStringElement::RENDER_STRIP)
			);
		}
		
		if ($flags & self::FLAG_SELECTED) {
			return sprintf('<option selected="selected" value="%s" data-html="%s">%s</option>',
				$value,
				htmlspecialchars($caption->render($ctx)),
				$caption->render($ctx, MetaStringElement::RENDER_STRIP)
			);
		}
		
		return sprintf('<option value="%s" data-html="%s">%s</option>',
			$value,
			htmlspecialchars($caption->render($ctx)),
			$caption->render($ctx, MetaStringElement::RENDER_STRIP)				
		);
	}
	
}

?>