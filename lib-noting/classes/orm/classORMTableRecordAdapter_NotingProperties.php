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

class ORMTableRecordAdapter_NotingProperties extends ORMTableRecordAdapter_NameValueCollection {
	
	//************************************************************************************
	public function __construct($oRecord) {
		if (!($oRecord instanceof INotableObject)) throw new InvalidArgumentException('oRecord is not INotableObject');
		
		parent::__construct($oRecord, 'noting_properties', 0, function() use ($oRecord) {
			$arr = array();
			$arr[] = new NameValuePair('objectID', $oRecord->notingGetObjectID());
			$arr[] = new NameValuePair('objectType', $oRecord->notingGetObjectType());
			return $arr;
		});
		
		$self = $this;
		$oRecord->getEvents(ORMTableRecord::EVENTS_TPL_RENDER)->add(function($key, $oContext) use ($self) {
			if ($key == 'PropetiesArray') return $self->getAssoc();
			return null;
		});
	}
	
}

?>