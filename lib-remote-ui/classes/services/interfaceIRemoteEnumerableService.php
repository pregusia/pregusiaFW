<?php

interface IRemoteEnumerableService extends IEnumerable, IRemoteService {
	
	/**
	 * @return int
	 */
	public function enumerableUsageType();
	
	/**
	 * @return Enum
	 */
	public function enumerableGetAllEnum();
	
	/**
	 * @param string $param
	 * @return ComplexString
	 */
	public function enumerableToString($param);
	
	/**
	 * @param string $text
	 * @return Enum
	 */
	public function enumerableSuggest($text);	
	
}

?>