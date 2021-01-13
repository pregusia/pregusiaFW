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
 * @NeedLibrary lib-templating
 *
 */
class ForeignKeyRenderableMod implements ITemplateRenderableMod {
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() {
		return 10;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		
	}
	
	//************************************************************************************
	public function getName() { return 'orm.fk.toString'; }
	
	//************************************************************************************
	/**
	 * @param TemplateRenderableProxyContext $oContext
	 * @param mixed $value
	 * @param array $params
	 */
	public function apply($oContext, $value, $params) {
		if (!$value) return '[none]';
		
		$oField = $oContext->getTag('ORMField');
		
		if ($oField instanceof ORMField) {
			$oValuesSource = $oField->getValuesSource();
			if ($oValuesSource instanceof ORMTable) {
				$oRecord = $oField->getRecord();
	
				if ($oRelation = $oRecord->getRelationFor($oValuesSource, $oField->getDefinition()->getName())) {
					// jesli u nas jest taka relacja, to jej uzywamy
					
					$tmp = $oRelation->getRecord();
					if ($tmp) {
						return htmlspecialchars($tmp->getEnumCaption());
					} else {
						return '[rel-not-found]';
					}
				} else {
					$oForeignRecord = $oValuesSource->getByPK($value);
					if ($oForeignRecord) {
						return htmlspecialchars($oForeignRecord->getEnumCaption());
					} else {
						return '[not-found]';
					}
				}
			} else {
				return '[not-orm-table]';
			}
		}
		
		return $value;
	}
	
}

?>