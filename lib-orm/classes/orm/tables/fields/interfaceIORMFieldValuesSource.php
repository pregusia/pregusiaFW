<?php

interface IORMFieldValuesSource extends IEnumerable {

	/**
	 * 
	 * @param ORMField $oField
	 * @param ORMTableRecord $oTableRecord
	 * @param ORMTable $oTable
	 */
	public function onInit($oField, $oTableRecord, $oTable);
	
}

?>