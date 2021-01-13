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


class UIActionsRenderer implements IUIRenderable {

	use TUIComponentHaving;

	/**
	 * @var UIActionsCollection
	 */
	private $oActions = null;
	
	private $area = '';
	private $limit = 0;
	
	//************************************************************************************
	public function __construct($oActions, $area='', $limit=4) {
		if (!($oActions instanceof UIActionsCollection)) throw new InvalidArgumentException('oActions is not UIActionsCollection');
		$this->oActions = $oActions;
		$this->area = $area;
		$this->limit = $limit;
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'Actions': return TemplateRenderableProxy::wrap($this->oActions->getActions());
			case 'area': return $this->area;
			case 'limit': return $this->limit;
			default: return '';
		}
	}
	
	//************************************************************************************
	public function uiRenderGetVariableName() { return 'ActionsRenderer'; }
	public function uiRenderGetTemplateLocation($ctx=null) { return ''; }
	
}

?>