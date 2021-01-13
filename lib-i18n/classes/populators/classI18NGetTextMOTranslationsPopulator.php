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


class I18NGetTextMOTranslationsPopulator implements II18NTranslationsPopulator {
	
	//************************************************************************************
	/**
	 * @param I18NApplicationComponent $oComponent
	 */
	public function populate($oComponent) {
		
		foreach(CodeBase::getLibraries() as $oLibrary) {
			
			foreach(LanguageEnum::getInstance()->getKeys() as $lang) {
				$path = sprintf('i18n/%s.mo', strtolower($lang));
				if ($oLibrary->exists($path)) {
					try {
						$oResource = $oLibrary->getResource($path);
						$translations = self::readMO($oResource);
						
						foreach($translations as $name => $oTranslation) {
							$oComponent->setTranslation($name, $lang, $oTranslation);
						}
					} catch(IOException $e) {
						Logger::warn('loading translations from ' . $path, $e);
					}
				}
			}
			
		}
		
	}
	
	//************************************************************************************
	/**
	 * @param CodeBaseLibraryResource $oResource
	 * @return I18NTranslation[]
	 */
	private static function readMO($oResource) {
		if (!($oResource instanceof CodeBaseLibraryResource)) throw new InvalidArgumentException('oResource is not CodeBaseLibraryResource');
		if (!$oResource->exists()) return array();
		
		$translations = array();
		$oReader = new GetTextMOReader($oResource->contents());
		
		$unknown1 = $oReader->readInt();
		$total = $oReader->readInt();
		$orginalsPosition = $oReader->readInt(); 
		$translatedPosition = $oReader->readInt();
		 
		$oStream->seekTo($orginalsPosition);
		$orginalsTable = $oReader->readIntArray($total * 2);
		
		$oStream->seekTo($translatedPosition);
		$translatedTable = $oReader->readIntArray($total * 2);
		
	
		for($i = 0;$i < $total; $i++) {
			$oReader->seekTo($orginalsTable[$i * 2 + 2]);
			$orginal = $oReader->read($orginalsTable[$i * 2 + 1]);
			
			$oReader->seekTo($translatedTable[$i * 2 + 2]);
			$translated = $oReader->read($translatedTable[$i * 2 + 1]);
			
			if ($original) {
				$chunks = explode("\x04", $original, 2);
				if (isset($chunks[1])) {
					$context = $chunks[0];
					$original = $chunks[1];
				} else {
					$context = '';
				}
	
				$chunks = explode("\x00", $original, 2);
	
				if (isset($chunks[1])) {
					$original = $chunks[0];
					$plural = $chunks[1];
				} else {
					$plural = '';
				}
	
				if ($translated !== '') {
					if ($plural === '') {
						$translations[$orginal] = I18NTranslation::CreateSimple($translated);
					} else {
						$arr = array();
						foreach (explode("\x00", $translated) as $pluralIndex => $pluralValue) {
							$arr[$pluralIndex] = $pluralValue;
						}
						$translations[$orginal] = I18NTranslation::CreatePlural($arr);
					}
				}
			}
		}
	
		return $translations;
	}
	
}

?>