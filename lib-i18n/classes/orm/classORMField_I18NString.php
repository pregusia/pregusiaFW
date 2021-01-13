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
class ORMField_I18NString extends ORMField {
	
	private $textID = 0;
	private $texts = false;
	
	//************************************************************************************
	public function setLang($lang, $val) {
		if ($this->texts === false) $this->loadTexts();
		if (!LanguageEnum::getInstance()->isValid($lang)) throw new InvalidArgumentException('Invalid language - ' . $lang);
		$this->texts[$lang] = $val;
		$this->changed = true;
	}
	
	//************************************************************************************
	public function getLang($lang) {
		if ($this->texts === false) $this->loadTexts();
		return $this->texts[$lang];
	}
	
	//************************************************************************************
	public function get() {
		if ($this->texts === false) $this->loadTexts();
		return $this->texts;
	}
	
	//************************************************************************************
	public function getAssoc() { 
		return $this->get();
	}
	
	//************************************************************************************
	/**
	 * @return I18NString
	 */
	public function getI18NString() {
		if ($this->texts === false) $this->loadTexts();
		return new I18NString($this->texts);
	}
	
	//************************************************************************************
	public function set($v) {
		if (is_array($v)) {
			foreach($v as $lang => $val) {
				if (LanguageEnum::getInstance()->isValid($lang)) {
					$this->setLang($lang, $val);
				}
			}
		}
		if ($v instanceof I18NString) {
			foreach($v->getAssoc() as $lang => $val) {
				$this->setLang($lang, $val);
			}
		}
	}
	
	//************************************************************************************
	private function loadTexts() {
		$this->texts = array();
		
		if ($this->textID) {
			$query = sprintf('SELECT * FROM i18n WHERE textID=%d', $this->textID);
			foreach($this->getORM()->getSQLStorage()->getRecords($query) as $oRow) {
				$lang = $oRow->getColumn('lang')->getValueRaw();
				$content = $oRow->getColumn('content')->getValueRaw();
				$this->texts[$lang] = $content;				
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param ISQLValueEscaper $oEscaper
	 */
	public function toSQL($oEscaper) {
		if ($this->texts !== false) {
			
			if ($this->textID == 0) {
				$id = intval($this->getORM()->getSQLStorage()->getFirstColumn('SELECT MAX(textID) FROM i18n'));
				$id += rand(2,6);
				$this->textID = $id;				
			}
			
			foreach($this->texts as $lang => $txt) {
				$this->getORM()->getSQLStorage()->replaceRecord('i18n', array(
					'textID' => $this->textID,
					'lang' => $lang,
					'content' => $txt	
				));
			}
			
		}
		return $this->textID;
	}
	
	//************************************************************************************
	public function isNull() {
		return false;
	}
	
	//************************************************************************************
	public function load($val) {
		$this->textID = intval($val);
		$this->changed = false;
	}
	
	//************************************************************************************
	public function tplRender($oContext) {
		return $this->getI18NString();
	}
	
}

?>