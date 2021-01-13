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


class ORMQuerySelect {

	private $order = '';
	private $groupBy = '';
	private $limit = '';
	
	/**
	 * @var ORMQueryConditionAndNamePair[]
	 */
	private $conditions = array();
	
	/**
	 * @var string[]
	 */
	private $rawSQLWhere = array();
	
	/**
	 * @var string[]
	 */
	private $rawSQLJoins = array();
	
	/**
	 * @var string[]
	 */
	private $rawSQLSelectExpressions = array();
	
	/**
	 * Glowna tabela na ktorej wywolywany jest select
	 * @var ORMTable
	 */
	private $oMainTable = null;
	
	/**
	 * Laczenia
	 * @var ORMJoin[]
	 */
	private $joins = array();
	
	/**
	 * Mapowanie aliasow tabel
	 * @var string => ORMTable
	 */
	private $aliases = array();
	
	/**
	 * Rekordy ktore sa znane a'priori
	 * @var ORMTableRecordAndRelationDefinitionPair[]
	 */
	private $knownRecords = array();
	
	private $explicitRelationsForces = array();

		
	//************************************************************************************
	public function getOrder() { return $this->order; }
	public function setOrder($v) {
		$this->order = sprintf('ORDER BY %s', $v);
	}
	
	//************************************************************************************
	public function getGroupBy() { return $this->groupBy; }
	public function setGroupBy($v) { $this->groupBy = $v; }
	
	//************************************************************************************
	public function setLimit($start,$num) {
		$this->limit = sprintf('LIMIT %d, %d', $start, $num);
	}
	
	//************************************************************************************
	public function addSQLWhere($sql) {
		$sql = trim($sql);
		if ($sql) {
			$this->rawSQLWhere[] = $sql;
		}
	}
	
	//************************************************************************************
	public function addSQLJoin($sql) {
		$sql = trim($sql);
		if ($sql) {
			$this->rawSQLJoins[] = $sql;
		}		
	}
	
	//************************************************************************************
	public function addSQLSelectExpression($sql) {
		$sql = trim($sql);
		if ($sql) {
			$this->rawSQLSelectExpressions[] = $sql;
		}		
	}	
	
	//************************************************************************************
	/**
	 * @param string $fieldName
	 * @param IORMQueryCondition $oCondition
	 */
	public function addCondition($fieldName, $oCondition) {
		$this->conditions[] = new ORMQueryConditionAndNamePair($fieldName, $oCondition);
	}
	
	//************************************************************************************
	public function addExplicitRelation($name) {
		$this->explicitRelationsForces[] = $name;
	}
	
	//************************************************************************************
	/**
	 * @return ORMTable
	 */
	public function getMainTable() { return $this->oMainTable; }
	
	//************************************************************************************
	/**
	 * @param ORMTable $oTable
	 */
	public function setMainTable($oTable) {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		
		$this->oMainTable = $oTable;
		$this->aliases = array();
		$this->aliases['a_main'] = $oTable;
		$this->joins = array();
		
		$stack = array();
		$stack[] = 'a_main';
		$this->addJoins($oTable, $stack);
	}
	
	//************************************************************************************
	/**
	 * @param ORMRelationDefinition $oRelationDef
	 * @param ORMTable $oTable
	 * @return boolean
	 */
	private function isKnown($oRelationDef, $oTable) {
		if (!($oRelationDef instanceof ORMRelationDefinition)) throw new InvalidArgumentException('oRelationDef is not ORMRelationDefinition');
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		
		foreach($this->knownRecords as $obj) {
			if ($obj->getRecord()->getTable() == $oTable && $obj->getRelationDefinition() == $oRelationDef) return true;
		}
		
		return false;
	}
	
	//************************************************************************************
	/**
	 * @param DBRelationDefinition $oRelationDef
	 */
	private function isRelationExplicit($oRelationDef) {
		if (!($oRelationDef instanceof ORMRelationDefinition)) throw new InvalidArgumentException('oRelationDef is not ORMRelationDefinition');
		if ($oRelationDef->isExplicit()) return true;
		
		if (in_array($oRelationDef->getName(), $this->explicitRelationsForces)) return true;
		
		return false;
	}
	
	//************************************************************************************
	/**
	 * Twozry joiny bazujac na relacji podanej tabeli
	 * @param ORMTable $oTable
	 */
	private function addJoins($oTable, &$stack) {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		if (count($stack) > 5) return;
		
		foreach($oTable->getRelations() as $oRelationDef) {
			if ($this->isRelationExplicit($oRelationDef) && !$oRelationDef->isMany($oTable)) {
				$oForeign = $oRelationDef->getForeignFor($oTable);
				$oLocal = $oRelationDef->getLocalFor($oTable);
				
				if (!$this->isKnown($oRelationDef, $oForeign->getTable())) {
					
					$localAlias = implode('$',$stack);
					$stack[] = str_replace('.', '_', $oRelationDef->getName());
					$foreignAlias = implode('$',$stack);
					
					$this->joins[] = new ORMJoin($oRelationDef->getName(), ORMJoin::JOIN_LEFT, $oLocal, $localAlias, $oForeign, $foreignAlias);
					$this->aliases[$foreignAlias] = $oForeign->getTable();
					
					$this->addJoins($oForeign->getTable(), $stack);
					
					array_pop($stack);
				}
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param ORMTable $oTable
	 */
	public function getAliasNameFor($oTable) {
		if (!($oTable instanceof ORMTable)) throw new InvalidArgumentException('oTable is not ORMTable');
		foreach($this->aliases as $name => $t) {
			if ($t === $oTable) return $name;
		}
		return '';
	}

	//************************************************************************************
	/**
	 * @param ORMRelationDefinition $oRelationDef
	 * @param ORMTableRecord $oRecord
	 */
	public function addKnownRecord($oRelationDef, $oRecord) {
		if (!($oRelationDef instanceof ORMRelationDefinition)) throw new InvalidArgumentException('oRelationDef is not ORMRelationDefinition');
		if (!($oRecord instanceof ORMTableRecord)) throw new InvalidArgumentException('oRecord is not ORMTableRecord');
		
		$this->knownRecords[] = new ORMTableRecordAndRelationDefinitionPair($oRecord, $oRelationDef);
	}
	
	//************************************************************************************
	/**
	 * @return ORMTableRecordAndRelationDefinitionPair[]
	 */
	public function getKnownRecords() {
		return $this->knownRecords;
	}
	
	//************************************************************************************
	/**
	 * @param ORM $oORM
	 */
	public function createSelectQuery($oORM) {
		if (!($oORM instanceof ORM)) throw new InvalidArgumentException('oORM is not ORM');
		if (!$this->oMainTable) throw new IllegalStateException('MainTable not set');
		
		// tworzenie zapytania
		$res = '';
		$res .= 'SELECT ' . UtilsArray::joinWithSuffix(array_keys($this->aliases), ', ', '.*') . ' ';
		
		if ($this->rawSQLSelectExpressions) {
			$res .= sprintf(', %s', implode(',', $this->rawSQLSelectExpressions));
		}
		
		$res .= 'FROM ' . $this->getMainTable()->getTableName() . ' a_main';

		foreach($this->joins as $oJoin) {
			$res .= $oJoin->createQuery() . ' ';
		}
		
		foreach($this->rawSQLJoins as $sql) {
			$res .= ' ' . $sql . ' ';
		}
		
		$res .= ' ';
		
		if ($this->conditions || $this->rawSQLWhere) {
			$res .= ' WHERE 1';
			
			foreach($this->conditions as $oCondition) {
				$res .= sprintf(' AND (%s) ', $oCondition->toSQL($oORM));
			}
			
			foreach($this->rawSQLWhere as $sql) {
				$res .= sprintf(' AND (%s) ', $sql);
			}
			$res .= ' ';
		}

		if ($this->groupBy) {
			$res .= ' GROUP BY ' . $this->groupBy . ' ';
		}
		
		if ($this->order) {
			$res .= $this->order . ' ';
		}
		
		if ($this->limit) {
			$res .= $this->limit . ' ';
		}
		
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param ORM $oORM
	 */
	public function createCountQuery($oORM) {
		if (!($oORM instanceof ORM)) throw new InvalidArgumentException('oORM is not ORM');
		if (!$this->oMainTable) throw new IllegalStateException('MainTable not set');
		
		// tworzenie zapytania
		$res = '';
		$res .= 'SELECT COUNT(*) AS `num` ';
		$res .= 'FROM ' . $this->getMainTable()->getTableName() . ' a_main';

		foreach($this->joins as $oJoin) {
			$res .= $oJoin->createQuery() . ' ';
		}
		
		foreach($this->rawSQLJoins as $sql) {
			$res .= ' ' . $sql . ' '; 
		}		
		
		$res .= ' ';
		
		if ($this->conditions || $this->rawSQLWhere) {
			$res .= ' WHERE 1';
			
			foreach($this->conditions as $oCondition) {
				$res .= sprintf(' AND (%s) ', $oCondition->toSQL($oORM));
			}
			
			foreach($this->rawSQLWhere as $sql) {
				$res .= sprintf(' AND (%s) ', $sql);
			}
			$res .= ' ';
		}
		
		if ($this->groupBy) {
			$res .= ' GROUP BY ' . $this->groupBy . ' ';
		}		
		
		if ($this->order) {
			$res .= $this->order . ' ';
		}
		if ($this->limit) {
			$res .= $this->limit . ' ';
		}
		
		return $res;
	}

	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 * @return ORMQuerySelect
	 */
	public static function Ensure($oSelect) {
		if ($oSelect) {
			if (!($oSelect instanceof ORMQuerySelect)) throw new InvalidArgumentException('oSelect is not ORMQuerySelect');
			return $oSelect;
		} else {
			return new ORMQuerySelect();
		}
	}

}

?>