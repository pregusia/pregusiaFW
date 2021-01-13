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

class MailerSender_PHPMail implements IMailerSender {

	//************************************************************************************
	/**
	 * @param Configuration $oConfig
	 */
	public function onInit($oConfig) {
		
	}
	
	//************************************************************************************
	/**
	 * @param MailerMail $oMail
	 * @return bool
	 */
	public function sendSingle($oMail) {
		if (!($oMail instanceof MailerMail)) throw new InvalidArgumentException('oMail is not MailerMail');
		
		$headers = array();
		$headers[] = sprintf('From: %s', $oMail->getFromMail());
		$headers[] = sprintf('Reply-To: %s', $oMail->getFromMail());
		$headers[] = sprintf('Content-Type: %s', $oMail->getContentType());
		
		return mail($oMail->getToMail(), $oMail->getSubject(), $oMail->getContentText(), implode("\r\n", $headers));
	}
	
	//************************************************************************************
	/**
	 * @param MailerMail[] $arr
	 */
	public function sendMany($arr) {
		UtilsArray::checkArgument($arr, 'MailerMail');
		$num = 0;
		
		foreach($arr as $oMail) {
			if ($this->sendSingle($oMail)) {
				$num += 1;
			}
		}
		
		return $num;
	}
	
}

?>