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


class UIAction implements IUIStylableElement, ITemplateRenderableSupplier {
	
	use TUIStylableElementDefault;
	use TTagsContainer;
	
	/**
	 * @var ComplexString
	 */
	private $title = null;
	
	/**
	 * @var ComplexString
	 */
	private $tooltip = null;
	
	private $url = '';
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getTitle() { return $this->title; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getToolTip() { return $this->tooltip; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getURL() { return $this->url; }
	
	//************************************************************************************
	public function __construct($title, $url, $elementClasses='', $toolTip='') {
		$this->title = ComplexString::Adapt($title);
		$this->tooltip = ComplexString::AdaptTrim($toolTip);
		$this->url = $url;
		foreach(explode(" ", $elementClasses) as $cls) {
			$cls = trim($cls);
			if ($cls) {
				$this->addElementClass($cls);
			}
		}
	}
	
	//************************************************************************************
	public function getElementClassesWithoutPrefixString($prefix) {
		$arr = array();
		foreach($this->elementClasses as $cls) {
			if (UtilsString::startsWith($cls, $prefix)) continue;
			$arr[] = $cls;
		}
		return implode(' ',$arr);
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		switch($key) {
			case 'title': return $this->title;
			case 'tooltip': return $this->tooltip;
			case 'url': return $this->url;
			case 'elementClasses': return $this->getElementClassesString();
			case 'elementClassesWithoutBtn': return $this->getElementClassesWithoutPrefixString('btn');
			case 'elementParams': return $this->getElementParamsString();
			case 'elementID': return $this->getElementID();
		}
		return '';
	}
	
}

?>