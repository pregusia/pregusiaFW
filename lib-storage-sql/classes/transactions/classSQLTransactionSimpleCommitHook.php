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

class SQLTransactionSimpleCommitHook implements ISQLTransactionHook {

	private $func = null;
	
	//************************************************************************************
	public function __construct($func) {
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		$this->func = $func;
	}
	
	//************************************************************************************
	/**
	 * @param SQLTransaction $oTransation
	 */
	public function onBegin($oTransation) {
		
	}
	
	//************************************************************************************
	/**
	 * @param SQLTransaction $oTransation
	 */	
	public function onCommit($oTransation) {
		$func = $this->func;
		$func();
	}
	
	//************************************************************************************
	/**
	 * @param SQLTransaction $oTransation
	 */	
	public function onRollback($oTransation) {
		
	}
	
	//************************************************************************************
	/**
	 * @param SQLStorage $oSQLStorage
	 * @param Closure $func
	 */
	public static function run($oSQLStorage, $func) {
		if (!($oSQLStorage instanceof SQLStorage)) throw new InvalidArgumentException('oSQLStorage is not SQLStorage');
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		
		if ($oTransaction = $oSQLStorage->getActiveTransaction()) {
			$hookName = sprintf('SQLTransactionSimpleCommitHook.%d', rand(0, 10000));
			$oTransaction->addHook($hookName, new SQLTransactionSimpleCommitHook($func));
		} else {
			$func();
		}
	}
	
}

?>