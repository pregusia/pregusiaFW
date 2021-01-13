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
 * @author pregusia
 * @NeedLibrary lib-orm
 *
 */
abstract class UIFormORM extends UIForm {
	
	/**
	 * @var ORMTableRecord
	 */
	protected $oRecord = null;
	
	//************************************************************************************
	/**
	 * @return ORMTableRecord
	 */
	public function getRecord() { return $this->oRecord; }

	//************************************************************************************
	/**
	 * @return ORMTable
	 */
	public function getTable() { return $this->getRecord() ? $this->getRecord()->getTable() : null; }

	//************************************************************************************
	/**
	 * @return SQLStorage
	 */
	public function getSQLStorage() { return $this->getTable()->getSQLStorage(); }
	
	//************************************************************************************
	/**
	 * @return ORM
	 */
	public function getORM() { return $this->getTable()->getORM(); }
	
	//************************************************************************************
	public function __construct($oController) {
		parent::__construct($oController);
	}
	
	//************************************************************************************
	/**
	 * @param ORMField $oField
	 * @param string $caption
	 * @param UIWidget $oWidget
	 * @return UIWidget
	 */
	public function addORMWidget($caption, $oField, $oWidget=null) {
		if (!($oField instanceof ORMField)) throw new InvalidArgumentException('oField is not ORMField');

		if (!$oWidget) $oWidget = UIWidgetORMFactory::createWidget($oField, $caption);
		if (!$oWidget) throw new InvalidArgumentException(sprintf('Could not create UIWidget from ORMField %s', get_class($oField)));
		
		$oWidget->callbackRegister(UIWidgetWithValue::CALLBACK_AFTER_READ, function($arg) use ($oField) {
			$oField->set($arg->getValue());
		});
		$oWidget->setValue($oField->get());
		
		return $this->addWidget($oWidget);
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		if ($key == 'Record') {
			return new TemplateRenderableProxy($this->getRecord());
		}
		return parent::tplRender($key, $oContext);
	}
	
}

?>