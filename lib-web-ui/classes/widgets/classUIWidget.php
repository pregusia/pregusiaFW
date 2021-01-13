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


abstract class UIWidget implements IUIRenderable, IUIStylableElement {
	
	use TTagsContainer;
	use TUIStylableElementDefault;
	use TCallbacksContainer;
	use TUIComponentHaving;

	protected $elementUniqueID = '';
	
	protected $name = '';
	protected $index = 0;

	/**
	 * @var ComplexString
	 */
	protected $caption = null;
	
	/**
	 * @var ComplexString
	 */
	protected $description = null;
	
	/**
	 * @var ComplexString
	 */
	protected $suffix = null;
	
	/**
	 * @var ComplexString
	 */
	protected $prefix = null;
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function setName($v) { $this->name = $v; return $this; }
	
	//************************************************************************************
	public function getCombinedName($suffix) {
		$args = func_get_args();
		array_unshift($args, $this->name);
		return implode('_', $args);
	}
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getCaption() { return $this->caption; }
	public function setCaption($v) { $this->caption = ComplexString::Adapt($v); return $this; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getDescription() { return $this->description; }
	public function setDescription($v) { $this->description = ComplexString::Adapt($v); return $this; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getSuffix() { return $this->suffix; }
	public function setSuffix($v) { $this->suffix = ComplexString::Adapt($v); return $this; }
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getPrefix() { return $this->prefix; }
	public function setPrefix($v) { $this->prefix = ComplexString::Adapt($v); return $this; }
	
	//************************************************************************************
	public function getIndex() { return $this->index; }
	public function setIndex($v) { $this->index = $v; return $this; }
	
	//************************************************************************************
	public function __construct($name, $caption) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('name is empty');
		
		$this->description = ComplexString::CreateEmpty();
		$this->suffix = ComplexString::CreateEmpty();
		$this->prefix = ComplexString::CreateEmpty();
		$this->caption = ComplexString::AdaptTrim($caption);
		
		$this->name = $name;
		$this->elementUniqueID = sprintf('uid_%s', uniqid());
	}

	//************************************************************************************
	/**
	 * @param WebResponseBase $oResponse
	 */
	public function adaptResponse($oResponse) {
		if ($oResponse instanceof WebResponseTwoLayersSiteLayout) {
			$oResponse->addStylesheetURL(genLink('ui.css'));
			$oResponse->addJavaScriptURL(genLink('ui.js'));
		} 
	}
	
	//************************************************************************************
	/**
	 * @param UIForm $oForm
	 */
	public function onAddedToForm($oForm) {
		
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) { return ''; }
	public function uiRenderGetVariableName() { return 'Widget'; }
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'name': return $this->name;
			case 'caption': return $this->caption;
			case 'hasCaption': return !$this->caption->isEmpty();
			case 'description': return $this->description;
			case 'hasDescription': return !$this->description->isEmpty();
			case 'prefix': return $this->prefix->render($oContext);
			case 'hasPrefix': return !$this->prefix->isEmpty();
			case 'suffix': return $this->suffix->render($oContext);
			case 'hasSuffix': return !$this->suffix->isEmpty();
			case 'index': return $this->index;
			case 'Tags': return UtilsArray::merge($oContext->getTags(), $this->getTags());
			
			case 'elementID': return $this->elementID ? $this->elementID : $this->elementUniqueID;
			case 'elementClasses': return $this->getElementClassesString();
			case 'elementParams': return $this->getElementParamsString();
			case 'elementUniqueID': return $this->elementUniqueID; 
			
			default: return '';
		}
	}
	
}

?>