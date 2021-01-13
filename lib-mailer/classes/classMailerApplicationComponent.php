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

class MailerApplicationComponent extends ApplicationComponent {
	
	const STAGE = 80;
	
	/**
	 * @var IMailerSender[]
	 */
	private $senders = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() { return 'mailer'; }
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function getStages() {
		return array(self::STAGE);
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if ($stage == self::STAGE) {
			$this->initSenders();
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return IMailerSender
	 */
	public function getSender($name='default') {
		if (!$this->senders[$name]) {
			throw new InvalidArgumentException(sprintf('Sender with name %s not found', $name));
		}
		return $this->senders[$name];
	}
	
	//************************************************************************************
	/**
	 * @param SQLStorage $oSQLStorage
	 * @param string $senderName
	 * @return IMailerSender
	 */
	public function getSQLTransactionAwareSender($oSQLStorage, $senderName='default') {
		CodeBase::ensureLibrary('lib-storage-sql', 'lib-mailer');
		
		if (!($oSQLStorage instanceof SQLStorage)) throw new InvalidArgumentException('oSQLStorage is not SQLStorage');
		if ($oTransaction = $oSQLStorage->getActiveTransaction()) {
			if (!$oTransaction->getHook('MailerSender_SQLTransactionProxy')) {
				$oTransaction->addHook('MailerSender_SQLTransactionProxy', new MailerSender_SQLTransactionProxy($this, $senderName));
			}
			
			return $oTransaction->getHook('MailerSender_SQLTransactionProxy');
			
		} else {
			return $this->getSender($senderName);
		}
	}
	
	//************************************************************************************
	private function initSenders() {
		$this->senders = array();
		
		$senders = $this->getConfig()->getArray('senders');
		foreach($senders as $name => $tmp) {
			if (!is_array($tmp)) throw new IllegalStateException(sprintf('Sender %s configuration is invalid (not array)', $name));
			if (!$tmp['className']) throw new IllegalStateException(sprintf('Sender %s configuration is invalid (empty cls name)', $name));
			
			$oClass = CodeBase::getClass($tmp['className']);
			if (!$oClass->isImplementing('IMailerSender')) throw new IllegalStateException(sprintf('Sender %s configuration is invalid (class %s is not IMailerSender)', $name, $oClass->getName()));

			$oSender = $oClass->ctorCreate();
			$oSender->onInit($this->getConfig()->getSubConfig(sprintf('senders.%s', $name)));
			$this->senders[$name] = $oSender;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $to
	 * @param string $toName
	 * @return MailerMail
	 */
	public function createMail($to, $toName='') {
		if (!$to) throw new InvalidArgumentException('to is empty');
		if (!filter_var($to, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('to is not email address');
		
		$oMail = new MailerMail();
		$oMail->setContentType('text/plain');
		$oMail->setToMail($to);
		$oMail->setToName($toName);
		
		$oMail->setFromMail($this->getConfig()->getValue('fromMail'));
		$oMail->setFromName($this->getConfig()->getValue('fromName'));
		
		return $oMail;
	}
	
	//************************************************************************************
	/**
	 * @param string $mailName
	 * @param string $toMail
	 * @param string $toName
	 * @param array $vars
	 * @param object $ctx
	 * @return MailerMail
	 */
	public function fillMailContent($oMail, $mailName, $vars=array(), $ctx=null) {
		if (!($oMail instanceof MailerMail)) throw new InvalidArgumentException('oMail is not MailerMail');
		if (!$mailName) throw new InvalidArgumentException('Empty mail name');

		$ok = false;
		
		foreach($this->getExtensions('IMailerContentSupplier') as $oExtension) {
			false && $oExtension = new IMailerContentSupplier();
			
			if ($oExtension->fill($mailName, $oMail, $ctx)) {
				$ok = true;
				break;
			}
		}
		
		if (!$ok) {
			throw new IllegalStateException(sprintf('Could not find content supplier for mail with name "%s"', $mailName));
		}
		
		$oTemplatingEngine = $this->getApplicationContext()->getComponent('templating',false);
		false && $oTemplatingEngine = new TemplatingEngineApplicationComponent();
		
		if ($oTemplatingEngine) {
			$oMail->setContentText($oTemplatingEngine->renderTemplateFromMemory($oMail->getContentText(), $vars));
			$oMail->setSubject($oTemplatingEngine->renderTemplateFromMemory($oMail->getSubject(), $vars));
		}
		
		return $oMail;
	}
	
}

?>