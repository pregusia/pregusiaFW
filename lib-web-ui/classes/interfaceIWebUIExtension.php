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


interface IWebUIExtension extends IApplicationComponentExtension {
	
	/**
	 * @param IUIRenderable $obj
	 * @param object $ctx
	 * @return string
	 */
	public function adaptTemplateLocation($obj, $ctx=null);
	
	/**
	 * @param object $obj
	 * @param UIActionsCollection $oActions
	 * @param string $area
	 */
	public function fillObjectActions($obj, $oActions, $area); 

	/**
	 * @param ORMField $oField
	 * @param string $name
	 * @param ComplexString $caption
	 * @return UIWidget
	 */
	public function createWidgetFromORMField($oField, $name, $caption);
		
	/**
	 * @param UIAssetsCollection $oAssets
	 */
	public function prepareAssets($oAssets);
	
}

?>