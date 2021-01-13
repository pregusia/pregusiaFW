<?php

class ORMJoin {
	
	const JOIN_LEFT = 1;
	const JOIN_INNER = 2;

	private $name = '';
	private $type = 1;

	private $localAlias = '';
	private $foreignAlias = '';
	
	private $oLocal = null;
	private $oForeign = null;

	//************************************************************************************
	public function getName() { return $this->name; }
	public function getType() { return $this->type; }

	//************************************************************************************
	public function getLocalAlias() { return $this->localAlias; }
	public function getForeignAlias() { return $this->foreignAlias; }
	
	//************************************************************************************
	/**
	 * @return ORMTableAndKeyPair
	 */
	public function getLocal() { return $this->oLocal; }
	
	//************************************************************************************
	/**
	 * @return ORMTableAndKeyPair
	 */
	public function getForeign() { return $this->oForeign; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param int $type
	 * @param ORMTableAndKeyPair $oLocal
	 * @param ORMTableAndKeyPair $oForeign
	 */
	public function __construct($name, $type, $oLocal, $localAlias, $oForeign, $foreignAlias) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		if (!($oLocal instanceof ORMTableAndKeyPair)) throw new InvalidArgumentException('Local is not ORMTableAndKeyPair');
		if (!($oForeign instanceof ORMTableAndKeyPair)) throw new InvalidArgumentException('Foreign is not ORMTableAndKeyPair');

		$this->type = $type;
		$this->oLocal = $oLocal;
		$this->localAlias = $localAlias;
		$this->oForeign = $oForeign;
		$this->foreignAlias = $foreignAlias;
		$this->name = $name;
		
		if ($type != self::JOIN_INNER && $type != self::JOIN_LEFT) throw new InvalidArgumentException('Invalid join');
		if ($this->getLocal()->getKey()->getCount() != $this->getForeign()->getKey()->getCount()) throw new InvalidArgumentException('LocalKey.count != ForeignKey.count');
	}
	
	//************************************************************************************
	/**
	 * Tworzy wiersz zapytania
	 */
	public function createQuery() {
		$res = ' ';
		if ($this->type == self::JOIN_INNER) $res .= 'INNER JOIN ';
		if ($this->type == self::JOIN_LEFT) $res .= 'LEFT JOIN ';
		$res .= $this->getForeign()->getTable()->getTableName() . ' ' . $this->foreignAlias;
		$res .= ' ON ';

		$tmp = array();
		for($i=0;$i<$this->getLocal()->getKey()->getCount();++$i) {
			$tmp[] = sprintf('%s.%s = %s.%s',
				$this->foreignAlias, $this->getForeign()->getKey()->get($i),
				$this->localAlias, $this->getLocal()->getKey()->get($i)
			);
		}
		$res .= implode(' AND ', $tmp);
		return $res;
	}
	
}

?>