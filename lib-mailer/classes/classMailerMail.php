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

class MailerMail {
	
	private $toMail = "";
	private $toName = "";
	private $fromMail = "";
	private $fromName = "";
	private $replyToName = '';
	private $replyToMail = '';
	private $subject = "";
	private $contentType = "";
	private $contentText = "";
	private $attachments = array();
	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getToMail() { return $this->toMail; }
	public function setToMail($v) { $this->toMail = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getToName() { return $this->toName; }
	public function setToName($v) { $this->toName = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFromMail() { return $this->fromMail; }
	public function setFromMail($v) { $this->fromMail = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFromName() { return $this->fromName; }
	public function setFromName($v) { $this->fromName = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getReplyToName() { return $this->replyToName; }
	public function setReplyToName($v) { $this->replyToName = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getReplyToMail() { return $this->replyToMail; }
	public function setReplyToMail($v) { $this->replyToMail = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getSubject() { return $this->subject; }
	public function setSubject($v) { $this->subject = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getContentType() { return $this->contentType; }
	public function setContentType($v) { $this->contentType = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getContentText() { return $this->contentText; }
	public function setContentText($v) { $this->contentText = $v; }
	
	//************************************************************************************
	/**
	 * @return MailerAttachment[]
	 */
	public function getAttachments() { return $this->attachments; }
	public function clearAttachments() { $this->attachments = array(); }
	
	//************************************************************************************
	public function addAttachment($v) { 
		if (!($v instanceof MailerAttachment)) throw new InvalidArgumentException('Argument is not MailerAttachment');
		$this->attachments[] = $v;
	}
	
	
}

?>