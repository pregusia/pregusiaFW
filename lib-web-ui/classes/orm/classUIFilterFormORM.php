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
class UIFilterFormORM extends UIFilterForm {
	
	//************************************************************************************
	/**
	 * @param ORMTableFieldDefinition $oFieldDefinition
	 * @param string $caption
	 * @param UIWidget $oWidget
	 * @return UIWidget
	 */
	public function addORMFieldWidget($caption, $oFieldDefinition, $oWidget=null) {
		if (!($oFieldDefinition instanceof ORMTableFieldDefinition)) throw new InvalidArgumentException('oFieldDefinition is not ORMTableFieldDefinition');

		$oField = $oFieldDefinition->createField();
		if (!$oWidget) $oWidget = UIWidgetORMFactory::createWidget($oField, $caption);
		if (!$oWidget) throw new InvalidArgumentException(sprintf('Could not create UIWidget from ORMField %s', get_class($oField)));
		
		$oWidget->setTag('ORMTableFieldDefinition', $oFieldDefinition);
		
		$oApplyFunc = function($arg, $w) {
			UIFilterFormORM::defaultApplyFunc($arg, $w);
		};
		
		if ($oWidget instanceof UIWidget_SelectInput) {
			$oWidget->flagSet(UIWidget_SelectInput::FLAG_WITH_NEUTRAL);
			$oWidget->flagSet(UIWidget_SelectInput::FLAG_MULTI);
		}
		
		return $this->addFilterWidget($oWidget, $oApplyFunc);
	}
	
	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 * @param UIWidgetWithValue $oWidget
	 */
	public static function defaultApplyFunc($oSelect, $oWidget) {
		$oFieldDefinition = $oWidget->getTag('ORMTableFieldDefinition');
		$oField = $oWidget->getTag('ORMField');
		
		if ($oWidget->isValueEmpty()) return;
		
		if (($oSelect instanceof ORMQuerySelect) && ($oFieldDefinition instanceof ORMTableFieldDefinition) && ($oField instanceof ORMField)) {
	
			$fieldName = $oFieldDefinition->getName();
			if (strpos($fieldName, '.') === false) $fieldName = 'a_main.' . $fieldName;
			
			if ($oField instanceof ORMField_String) {
				$oSelect->addCondition($fieldName, ORMQueryConditionFactory::makeLike($oWidget->getValueString()));
			}
			elseif ($oField instanceof ORMField_Integer) {
				$val = $oWidget->getValueString();
				if (strpos($val,',') !== false) {
					$arr = array();
					foreach(explode(',',$val) as $s) {
						if ($s = intval(trim($s))) {
							$arr[] = $s;
						}
					}
					$oSelect->addCondition($fieldName, ORMQueryConditionFactory::makeIn($arr));
				} else {
					$oSelect->addCondition($fieldName, ORMQueryConditionFactory::makeEqual($val));
				}
			}
			else {
				$oSelect->addCondition($fieldName, ORMQueryConditionFactory::makeEqual($oWidget->getValueString()));
			}
		}
	}
	
}

?>