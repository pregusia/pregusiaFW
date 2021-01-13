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


interface IORMExtension extends IApplicationComponentExtension {
	
	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 */
	public function onAfterCreated($oORM, $oRecord);

	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 */
	public function onAfterLoad($oORM, $oRecord);
	
	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 */
	public function onBeforeAdd($oORM, $oRecord);
	
	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 */
	public function onAfterAdd($oORM, $oRecord);
	
	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 */
	public function onBeforeUpdate($oORM, $oRecord);
	
	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 * @param bool $changed
	 */
	public function onAfterUpdate($oORM, $oRecord, $changed);
	
	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 */
	public function onBeforeDelete($oORM, $oRecord);
	
	/**
	 * @param ORM $oORM
	 * @param ORMTableRecord $oRecord
	 */
	public function onAfterDelete($oORM, $oRecord);
	
}

?>