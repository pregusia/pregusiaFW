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

class UIWidget_NotingTagsInput extends UIWidget_TagsInput {

	/**
	 * @var ORMTableRecordAdapter_NotingTags
	 */
	private $oAdapter = null;
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $caption
	 * @param ORMTableRecordAdapter_NotingTags $oAdapter
	 * @param IEnumerable $oEnumerable
	 */
	public function __construct($name, $caption, $oAdapter, $oEnumerable) {
		parent::__construct($name, $caption, $oEnumerable);
		if (!($oAdapter instanceof ORMTableRecordAdapter_NotingTags)) throw new InvalidArgumentException('oAdapter is not ORMTableRecordAdapter_NotingTags');
		$this->oAdapter = $oAdapter;
		$this->value = $this->oAdapter->getTags();
	}
	
	//************************************************************************************
	protected function onRead($oRequest) {
		parent::onRead($oRequest);
		$this->oAdapter->clear();
		foreach($this->value as $tag) {
			$this->oAdapter->add($tag);
		}
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-web-ui:UIWidget.TagsInput';
	}
	
}

?>