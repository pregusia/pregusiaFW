<?php

class ORMKey {

	private $fields = array();

	//**************************************************************
	public function __construct($v) {
		if (is_array($v)) {
			$this->fields = $v;
		} else {
			foreach(func_get_args() as $v) {
				$this->fields[] = $v;
			}
		}
	}

	//************************************************************************************
	public function getFields() {
		return $this->fields;
	}

	//************************************************************************************
	public function get($i) {
		return $this->fields[$i];
	}

	//************************************************************************************
	public function getFirst() {
		return UtilsArray::getFirst($this->fields);
	}
	
	//************************************************************************************
	public function getCount() {
		return count($this->fields);
	}
	
	//************************************************************************************
	public function contains($fieldName) {
		return in_array($fieldName, $this->fields);
	}
	
	//************************************************************************************
	/**
	 * Tworzy wyrazenie w ktorym <tableName>.oKey = (oRecord).get(oRecordKey) 
	 * 
	 * @param string $tableName
	 * @param ORMKey $oKey 
	 * @param ORMTableRecord $oRecord
	 * @param ORMKey $oRecordKey
	 */
	public static function createEqualCondition($tableName, $oKey, $oRecord, $oRecordKey) {
		if (!($oKey instanceof ORMKey)) throw new InvalidArgumentException('oKey is not ORMKey');
		if (!($oRecordKey instanceof ORMKey)) throw new InvalidArgumentException('oRecordKey is not ORMKey');
		if (!($oRecord instanceof ORMTableRecord)) throw new InvalidArgumentException('oRecord is not ORMTableRecord');
		
		$oStorage = $oRecord->getSQLStorage();
		
		$res = '(1';
		if ($oKey->getCount() != $oRecordKey->getCount()) throw new InvalidArgumentException('Invalid key sizes');
		
		for($i=0;$i<$oKey->getCount();++$i) {
			$res .= sprintf(' AND %s.%s = %s',
				$tableName,
				$oKey->get($i),
				$oRecord->getField($oRecordKey->get($i))->toSQL($oStorage) 
			); 
		}
		
		$res .= ')';
		return $res;
	}


}

?>