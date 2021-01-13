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
class UIWidgetORMFactory {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * @return WebUIApplicationComponent
	 */
	private static function getUIComponent() {
		return ApplicationContext::getCurrent()->getComponent('web.ui');
	}
	
	//************************************************************************************
	/**
	 * @param ORMField $oField
	 * @param string $name
	 * @param string $caption
	 * @return UIWidget
	 */
	private static function defaultORMFieldAdapter($oField, $name, $caption) {
		if ($oField instanceof ORMField_Flags) {
			$oSource = $oField->getValuesSource();
			if ($oSource) {
				if ($oSource instanceof FlagsEnum) {
					return new UIWidget_Flags($name, $caption, $oSource);
				} else {
					throw new IllegalStateException('Values source ' . get_class($oSource) . ' is not FlagsEnum');
				}
			} else {
				throw new IllegalStateException('Empty values source');
			}
		}
		
		if ((($oField instanceof ORMField_String) || ($oField instanceof ORMField_Integer)) && ($oSource = $oField->getValuesSource())) {
			// jakas enumeracja/flagi
			if ($oSource instanceof IEnumerable) {
				if ($oSource->enumerableUsageType() == IEnumerable::USAGE_SUGGEST) {
					return new UIWidget_SuggestInput($name, $caption, $oSource);
				}
				elseif ($oSource->enumerableUsageType() == IEnumerable::USAGE_SIMPLE) {
					$oWidget = new UIWidget_SelectInput($name, $caption, $oSource);
					if ($oField->getDefinition()->isNullable() || $oField->getDefinition()->getFkZeroAllow()) {
						$oWidget->flagSet(UIWidget_SelectInput::FLAG_WITH_NEUTRAL);
					}
					return $oWidget;
				}
				else {
					throw new IllegalStateException('Invalid IEnumerable usageType');
				}
			} else {
				throw new IllegalStateException('Unknown values source - ' . get_class($oSource));
			}
		}
		
		
		if ($oField instanceof ORMField_Text) return new UIWidget_TextInput($name, $caption);		
		if ($oField instanceof ORMField_String) return new UIWidget_StringInput($name, $caption);
		if ($oField instanceof ORMField_Integer) return new UIWidget_IntegerInput($name, $caption);
		if ($oField instanceof ORMField_Float) return new UIWidget_FloatInput($name, $caption);
		if ($oField instanceof ORMField_Decimal) return new UIWidget_DecimalInput($name, $caption, $oField->getDefinition()->getLength(), $oField->getDefinition()->getPrecision());
		
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param ORMField $oField
	 * @param string $caption
	 */
	public static function createWidget($oField, $caption) {
		if (!($oField instanceof ORMField)) throw new InvalidArgumentException('oField is not ORMField');
		
		$oWidget = null;
		
		foreach(self::getUIComponent()->getUIExtensions() as $oExtension) {
			$res = $oExtension->createWidgetFromORMField($oField, $oField->getDefinition()->getName(), $caption);
			if ($res) {
				$oWidget = $res;
				break;
			}
		}
		
		if (!$oWidget) {
			$oWidget = self::defaultORMFieldAdapter($oField, $oField->getDefinition()->getName(), $caption);
		}
		
		if ($oWidget) {
			$oWidget->setTag('ORMField', $oField);
		}
		
		return $oWidget;
	}
	
}

?>