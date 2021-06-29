<?php

/**
 * Instancja komponentu od ORMa
 * W ogolnym przypadku moze byc wiecej niz jedna taka instancje - tworzone one sa dla kazdego sql.storage
 * @author pregusia
 *
 */
class ORM implements IClassInstantinatorAdapter {
	
	/**
	 * @var ORMApplicationComponent
	 */
	private $oComponent = null;
	
	/**
	 * @var SQLStorage
	 */
	private $oStorage = null;
	
	/**
	 * @var ORMTable[]
	 */
	private $tables = array();
	
	/**
	 * @var ORMRelationDefinition[]
	 */
	private $relations = array();
	
	//************************************************************************************
	/**
	 * @return ORMApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	/**
	 * @return SQLStorage
	 */
	public function getSQLStorage() { return $this->oStorage; }
	
	//************************************************************************************
	/**
	 * @return ORMTable[]
	 */
	public function getTables() { return $this->tables; }
	
	//************************************************************************************
	/**
	 * @return ORMRelationDefinition[]
	 */
	public function getRelations() { return $this->relations; }
	
	//************************************************************************************
	/**
	 * @param string $tableName
	 * @return ORMTable
	 */
	public function getTable($tableName) { return $this->tables[$tableName]; }
	
	//************************************************************************************
	public function __construct($oComponent, $oStorage) {
		if (!($oComponent instanceof ORMApplicationComponent)) throw new InvalidArgumentException('oComponent is not ORMApplicationComponent');
		if (!($oStorage instanceof SQLStorage)) throw new InvalidArgumentException('oStorage is not SQLStorage');
		$this->oStorage = $oStorage;
		$this->oComponent = $oComponent;
		
		// wyszukujamy wszystkie ORMTable i tworzymy ich instancje
		foreach(CodeBase::getClassesExtending('ORMTable') as $oClass) {
			if ($oClass->isAbstract()) continue;
			
			$storageName = $oClass->getConstantValue('SQL_STORAGE_NAME');
			if (!$storageName) {
				throw new IllegalStateException(sprintf('Class %s extends ORMTable but dont have SQL_STORAGE_NAME constant', $oClass->getName()));
			}
			if ($storageName != $oStorage->getName()) continue;
			
			$oTable = $oClass->getInstance();
			false && $oTable = new ORMTable();
			
			$this->tables[$oTable->getTableName()] = $oTable;
			$oTable->setORM($this);
		}
		
		// teraz wyszukujemy wszystkie instancje IORMRelationsSupplier i ustawiamy relacje
		if (true) {
			$oInterface = CodeBase::getInterface('IORMRelationsSupplier');
			foreach($oInterface->getAllInstances() as $oSupplier) {
				false && $oSupplier = new IORMRelationsSupplier();
				if ($oSupplier->getSQLStorageName() == $this->getSQLStorage()->getName()) {
					$oSupplier->process($this);
				}				
			}
		}
		
		// teraz rejestrujemy IClassInstantinatorAdapter aby nie mozna bylo juz tworzyc wiecej klas ORMTable bezposrednio
		// jedynie jako manager
		CodeBase::registerInstantinatorAdapter($this);
	}
	
	//************************************************************************************
	/**
	 * @param CodeBaseDeclaredClass $oClass
	 * @return bool
	 */
	public function matches($oClass) {
		if ($oClass->isAbstract()) return false;
		return $oClass->isExtending('ORMTable');
	}
	
	//************************************************************************************
	/**
	 * @param CodeBaseDeclaredClass $oClass
	 * @return object
	 */
	public function getInstanceOf($oClass) {
		foreach($this->tables as $oTable) {
			if ($oClass->isInstanceOf($oTable)) {
				return $oTable;
			}
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param ORMTable $oTable
	 * @return ORMRelationDefinition[]
	 */
	public function getRelationsFor($oTable) {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		$arr = array();
		foreach($this->relations as $oRelation) {
			if ($oRelation->has($oTable)) {
				$arr[] = $oRelation;
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param ORMTableAndKeyPair $sideOne
	 * @param ORMTableAndKeyPair $sideTwo
	 * @param int $flags
	 * @return ORMRelationDefinition
	 */
	public function registerRelation($name, $sideOne, $sideTwo, $flags=0) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		if (isset($this->relations[$name])) throw new InvalidArgumentException('Relation with name - ' . $name . ' - already registered');
		
		if (!($sideOne instanceof ORMTableAndKeyPair)) throw new InvalidArgumentException('sideOne is not ORMTableAndKeyPair');
		if (!($sideTwo instanceof ORMTableAndKeyPair)) throw new InvalidArgumentException('sideTwo is not ORMTableAndKeyPair');
		
		$oDef = new ORMRelationDefinition($name, $sideOne, $sideTwo, $flags);
		$this->relations[$name] = $oDef;
		return $oDef; 
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $col1
	 * @param string $col2
	 * @return ORMRelationDefinition
	 */
	public function registerRelationHelper($name, $col1, $col2, $flags=0) {
		list($col1_tbl, $col1_name) = explode('.',$col1,2);
		list($col2_tbl, $col2_name) = explode('.',$col2,2);
		
		$oTable1 = $this->getTable($col1_tbl);
		if (!$oTable1) throw new ORMException(sprintf('Table %s not found in ORM %s', $col1_tbl, $this->getSQLStorage()->getName()));

		$oTable2 = $this->getTable($col2_tbl);
		if (!$oTable2) throw new ORMException(sprintf('Table %s not found in ORM %s', $col2_tbl, $this->getSQLStorage()->getName()));
		
		
		return $this->registerRelation($name,
			new ORMTableAndKeyPair($oTable1, new ORMKey($col1_name)),
			new ORMTableAndKeyPair($oTable2, new ORMKey($col2_name)),
			$flags
		);
	}
	
	//************************************************************************************
	/**
	 * @param mixed $val
	 * @return string
	 */
	public function toSQL($val) {
		return $this->getSQLStorage()->getTypesMapper()->toSQL($val, $this->getSQLStorage());
	}
	
	//************************************************************************************
	/**
	 * @param string $tableName
	 * @return ORMTable
	 */
	public static function internalGetTableStatic($tableName) {
		$oComponent = ApplicationContext::getCurrent()->getComponent('orm');
		false && $oComponent = new ORMApplicationComponent();
		
		foreach($oComponent->getORMs() as $oORM) {
			if ($oORM->getTable($tableName)) {
				return $oORM->getTable($tableName);
			}
		}
		
		throw new ORMException(sprintf('Table %s not found', $tableName));
	}
	
}

?>