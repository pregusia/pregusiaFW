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

/**
 * @author pregusia
 * @NeedLibrary lib-storage-sql
 *
 */
class MailerSender_SQLTransactionProxy implements IMailerSender, ISQLTransactionHook {
	
	/**
	 * @var MailerApplicationComponent
	 */
	private $oComponent = null;
	
	/**
	 * @var MailerMail[]
	 */
	private $toSend = array();
	private $senderName = '';
	
	
	//************************************************************************************
	public function __construct($oComponent, $senderName) {
		if (!($oComponent instanceof MailerApplicationComponent)) throw new InvalidArgumentException('oComponent is not MailerApplicationComponent');
		if (!$senderName) throw new InvalidArgumentException('Empty senderName');
		
		$this->oComponent = $oComponent;
		$this->senderName = $senderName;
	}
	
	//************************************************************************************
	/**
	 * @param Configuration $oConfig
	 */
	public function onInit($oConfig) {
		
	}
	
	//************************************************************************************
	/**
	 * @param MailerMail $oMail
	 */
	public function sendSingle($oMail) {
		if (!($oMail instanceof MailerMail)) throw new InvalidArgumentException('oMail is not MailerMail');
		$this->toSend[] = $oMail;
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param MailerMail[] $arr
	 */
	public function sendMany($arr) {
		UtilsArray::checkArgument($arr, 'MailerMail');
		foreach($arr as $oMail) {
			$this->sendSingle($oMail);
		}
		return count($arr);
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
		$this->oComponent->getSender($this->senderName)->sendMany($this->toSend);
	}
	
	//************************************************************************************
	/**
	 * @param SQLTransaction $oTransation
	 */	
	public function onRollback($oTransation) {
		
	}
	
}

?>