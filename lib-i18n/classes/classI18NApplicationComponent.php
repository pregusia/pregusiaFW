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


class I18NApplicationComponent extends ApplicationComponent {
	
	const STAGE = 80;
	
	private $currentLanguage = '';
	
	/**
	 * @var I18NTranslation[]
	 */
	private $translations = array();
	
	//************************************************************************************
	public function getName() { return 'i18n'; }
	public function getStages() { return array(self::STAGE); }
	public function onInit($stage) { }

	//************************************************************************************
	public function getCurrentLanguage() {
		return $this->currentLanguage;
	}
	
	//************************************************************************************
	/**
	 * @return II18NCurrentLanguageResolver[]
	 */
	public function getCurrentLanguageResolvers() {
		return $this->getExtensions('II18NCurrentLanguageResolver');
	}
	
	//************************************************************************************
	/**
	 * @return I18NTranslation[]
	 */
	public function getTranslations() {
		return $this->translations;
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if ($stage == self::STAGE) {
			LanguageEnum::getInstance();
			
			$this->loadTranslations();
			
			
			foreach($this->getCurrentLanguageResolvers() as $oResolver) {
				$lang = $oResolver->resolveCurrentLanguage();
				if ($lang) {
					$this->currentLanguage = strtoupper($lang);
					break;
				}
			}
			
			if (!LanguageEnum::getInstance()->isValid($this->currentLanguage)) {
				$this->currentLanguage = LanguageEnum::getInstance()->getFirstKey();
			}
		}
	}
	
	//************************************************************************************
	private function loadTranslations() {
		foreach(CodeBase::getInterface('II18NTranslationsPopulator')->getAllInstances() as $oPopulator) {
			false && $oPopulator = new II18NTranslationsPopulator();
			$oPopulator->populate($this);
		}
	}
	
	//************************************************************************************
	private static function getTranslationKey($name, $lang) {
		return $lang . '/' . $name;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $lang
	 * @param I18NTranslation $oTranslation
	 * @return bool
	 */
	public function setTranslation($name, $lang, $oTranslation) {
		if (!($oTranslation instanceof I18NTranslation)) throw new InvalidArgumentException('oTranslation is not I18NTranslation');
		if (!LanguageEnum::getInstance()->isValid($lang)) return false;
		if (!$name) return false;
		
		$this->translations[self::getTranslationKey($name, $lang)] = $oTranslation;
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $lang
	 * @return I18NTranslation
	 */
	public function getTranslation($name, $lang) {
		return $this->translations[self::getTranslationKey($name, $lang)];
	}
	
	//************************************************************************************
	public function translate($singular, $plural='', $count=0, $lang='') {
		if (!$lang) $lang = $this->currentLanguage;
		$count = intval($count);
		if (!$singular) return '';
		$oTranslation = $this->getTranslation($singular, $lang);
		if (!$oTranslation) return $singular;
		
		
		if ($plural && abs($count) > 0) {
			if (abs($count) == 1) return $oTranslation->getPlural(0);
			if (abs($count) == 2 || abs($count) == 3 || abs($count) == 4) return $oTranslation->getPlural(1);
			return $oTranslation->getPlural(2);
		} else {
			return $oTranslation->getTranslated();
		}
	}
	
}

?>