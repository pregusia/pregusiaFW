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


class UIWidgetsGroup implements ITemplateRenderableSupplier {
	
	private $name = '';
	private $title = '';
	
	/**
	 * @var UIWidget[]
	 */
	private $widgets = array();
	
	//************************************************************************************
	public function getName() { return $this->name; }
	public function setName($v) { $this->name = $v; }
	
	//************************************************************************************
	public function getTitle() { return $this->title; }
	public function setTitle($v) { $this->title = $v; }
	
	//************************************************************************************
	/**
	 * @return UIWidget[]
	 */
	public function getWidgets() { return $this->widgets; }
	
	//************************************************************************************
	/**
	 * @param UIWidget $oWidget
	 */
	public function addWidget($oWidget) {
		if (!($oWidget instanceof UIWidget)) throw new InvalidArgumentException('oWidget is not UIWidget');
		if ($this->widgets[$oWidget->getName()]) throw new IllegalStateException(sprintf('Widget with name "%s" already exists in group', $oWidget->getName()));
		$this->widgets[$oWidget->getName()] = $oWidget;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return UIWidget
	 */
	public function getWidget($name) {
		return $this->widgets[$name];
	}
	
	//************************************************************************************
	public function __construct($name, $title) {
		$this->name = $name;
		$this->title = $title;
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		switch($key) {
			case 'title': return $this->title;
			case 'name': return $this->name;
			case 'Widgets': return TemplateRenderableProxy::wrap($this->widgets);
			case 'WidgetsNames': return array_keys($this->widgets);
			default: return '';
		}
	}
	
	
}

?>