<?php

class BlockLanguage_Entry {
	
	/**
	 * @var string
	 */
	private $name = '';
	
	/**
	 * @var float[]
	 * @var string[]
	 * @var BlockLanguage_EntriesCollection[]
	 */
	private $arguments = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() { return $this->name; }
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getArgumentsCount() { return count($this->arguments); }
	
	//************************************************************************************
	public function __construct($name) {
		$name = trim($name);
		if (!$name) throw new InvalidArgumentException('Empty name');
		$this->name = $name;
		$this->arguments = array();
	}
	
	//************************************************************************************
	/**
	 * @param int $nr
	 * @return bool
	 */
	public function hasArgument($nr) {
		return $nr >= 0 && $nr < count($this->arguments);
	}
	
	//************************************************************************************
	/**
	 * @param int $nr
	 * @return bool
	 */
	public function isArgumentPrimitive($nr) {
		if (!$this->hasArgument($nr)) return false;
		return is_string($this->arguments[$nr]) || is_float($this->arguments[$nr]);
	}
	
	//************************************************************************************
	/**
	 * @param int $nr
	 * @return bool
	 */
	public function isArgumentBlock($nr) {
		if (!$this->hasArgument($nr)) return false;
		return ($this->arguments[$nr] instanceof BlockLanguage_EntriesCollection);
	}
	
	
	//************************************************************************************
	/**
	 * @param int $nr
	 * @return string
	 */
	public function getArgumentString($nr) {
		if (!$this->isArgumentPrimitive($nr)) return '';
		return strval($this->arguments[$nr]);
	}
	
	//************************************************************************************
	/**
	 * @param int $nr
	 * @return int
	 */
	public function getArgumentInt($nr) {
		if (!$this->isArgumentPrimitive($nr)) return '';
		return intval($this->arguments[$nr]);
	}
	
	//************************************************************************************
	/**
	 * @param int $nr
	 * @return float
	 */
	public function getArgumentFloat($nr) {
		if (!$this->isArgumentPrimitive($nr)) return '';
		return floatval($this->arguments[$nr]);
	}
	
	//************************************************************************************
	/**
	 * @param int $nr
	 * @return BlockLanguage_EntriesCollection
	 */
	public function getArgumentBlock($nr) {
		if (!$this->isArgumentBlock($nr)) return null;
		return $this->arguments[$nr];		
	}
	
	
	//************************************************************************************
	/**
	 * @param string $val
	 */
	public function addArgumentString($val) {
		$this->arguments[] = strval($val);
	}
	
	//************************************************************************************
	/**
	 * @param float $val
	 */
	public function addArgumentNumber($val) {
		$this->arguments[] = floatval($val);
	}
	
	//************************************************************************************
	/**
	 * @param BlockLanguage_EntriesCollection $oCollection
	 */
	public function addArgumentBlock($oCollection) {
		if (!($oCollection instanceof BlockLanguage_EntriesCollection)) throw new InvalidArgumentException('oCollection is not BlockLanguage_EntriesCollection');
		$this->arguments[] = $oCollection;
	}
	
}

?>