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


/**
 * 
 * @author pregusia
 * @NeedLibrary lib-orm
 *
 */
class UIWidget_ORMIntCollectionEnumerableInput extends UIWidget_IntCollectionEnumerableInput {
	
	/**
	 * @var ORMTableRecordAdapter_IntCollection
	 */
	private $oAdapter = null;
	
	//************************************************************************************
	/**
	 * @return ORMTableRecordAdapter_IntCollection
	 */
	public function getAdapter() { return $this->oAdapter; }
	
	//************************************************************************************
	public function __construct($name, $caption, $oEnumerable, $oAdapter) {
		if (!($oAdapter instanceof ORMTableRecordAdapter_IntCollection)) throw new InvalidArgumentException('oAdapter is not ORMTableRecordAdapter_IntCollection');
		parent::__construct($name, $caption, $oEnumerable);
		$this->oAdapter = $oAdapter;
		$this->value = $oAdapter->getAll();
	}
	
	//************************************************************************************
	protected function onRead($oRequest) {
		parent::onRead($oRequest);
		$this->oAdapter->clear();
		$this->oAdapter->addAll($this->value);
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-web-ui:UIWidget.IntCollectionEnumerableInput';
	}
	
}

?>