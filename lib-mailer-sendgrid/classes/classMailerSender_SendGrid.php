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

class MailerSender_SendGrid implements IMailerSender {

	private $apiKey = '';
	private $url = '';
	
	//************************************************************************************
	/**
	 * @param Configuration $oConfig
	 */
	public function onInit($oConfig) {
		if (!$oConfig->hasKey('key')) throw new ConfigurationException('key not present in SendGrid config');
		if (!$oConfig->hasKey('url')) throw new ConfigurationException('url not present in SendGrid config');
		
		$this->apiKey = $oConfig->getValue('key');
		$this->url = $oConfig->getValue('url');
	}
	
	//************************************************************************************
	/**
	 * @param MailerMail $oMail
	 * @return bool
	 */
	public function sendSingle($oMail) {
		if (!($oMail instanceof MailerMail)) throw new InvalidArgumentException('oMail is not MailerMail');
		
		$jsonRequest = array(
			'personalizations' => array(),
			'content' => array(),
			'from' => array(
				'email' => $oMail->getFromMail(),
				'name' => $oMail->getFromName(),	
			)
		);
		$jsonRequest['personalizations'][] = array(
			'subject' => $oMail->getSubject(),
			'to' => array(
				array(
					'email' => $oMail->getToMail(),
					'name' => $oMail->getToName(),
				)
			)
		);
		$jsonRequest['content'][] = array(
			'type' => $oMail->getContentType(),
			'value' => $oMail->getContentText()	
		);
		
		if ($oMail->getAttachments()) {
			$jsonRequest['attachments'] = array();
			foreach($oMail->getAttachments() as $oAttachment) {
				$jsonRequest['attachments'][] = array(
					'content' => $oAttachment->getContentData()->getDataBase64(),
					'type' => $oAttachment->getContentType(),
					'filename' => $oAttachment->getFileName(),
				);
			}
		}
		if ($oMail->getReplyToMail() && $oMail->getReplyToName()) {
			$jsonRequest['reply_to'] = array(
				'email' => $oMail->getReplyToMail(),
				'name' => $oMail->getReplyToName(),	
			);
		}
		
		$oClient = HTTPClient_CURL::Create($this->url);
		$oClient->setMethod(HTTPMethod::POST);
		$oClient->getHeaders()->putMulti('Authorization', sprintf('Bearer %s',$this->apiKey));
		$oClient->setContentType('application/json');
		$oClient->setRequestContent(json_encode($jsonRequest));
		$oResponse = $oClient->run();

		if ($oResponse->getStatusCode() < 200 || $oResponse->getStatusCode() >= 300) {
			throw new IOException(sprintf('SendGrid API response statusCode=%d', $oResponse->getStatusCode()));
		}
		
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param MailerMail[] $arr
	 * @return int
	 */
	public function sendMany($arr) {
		UtilsArray::checkArgument($arr, 'MailerMail');
		foreach($arr as $oMail) {
			$this->sendSingle($oMail);
		}		
		return count($arr);
	}
	
	
	//************************************************************************************
	public function sendTemplate($oMail, $templateID, $variables, $categories) {
		if (!($oMail instanceof MailerMail)) throw new InvalidArgumentException('oMail is not MailerMail');
		
		$jsonRequest = array(
			'personalizations' => array(),
			'categories' => array(),
			'from' => array(
				'email' => $oMail->getFromMail(),
				'name' => $oMail->getFromName(),	
			)
		);
		$jsonRequest['personalizations'][] = array(
			'dynamic_template_data' => $variables,
			'to' => array(
				array(
					'email' => $oMail->getToMail(),
					'name' => $oMail->getToName(),
				)
			)
		);
		$jsonRequest['template_id'] = $templateID;

		foreach($categories as $cat) {
			$cat = trim($cat);
			if ($cat) {
				$jsonRequest['categories'][] = $cat;
			}
		}
		
		if ($oMail->getReplyToMail() && $oMail->getReplyToName()) {
			$jsonRequest['reply_to'] = array(
				'email' => $oMail->getReplyToMail(),
				'name' => $oMail->getReplyToName(),	
			);
		}		
		
		if ($oMail->getAttachments()) {
			$jsonRequest['attachments'] = array();
			foreach($oMail->getAttachments() as $oAttachment) {
				$jsonRequest['attachments'][] = array(
					'content' => $oAttachment->getContentData()->getDataBase64(),
					'type' => $oAttachment->getContentType(),
					'filename' => $oAttachment->getFileName(),
				);
			}
		}		
		
		
		$oClient = HTTPClient_CURL::Create($this->url);
		$oClient->setMethod(HTTPMethod::POST);
		$oClient->getHeaders()->putMulti('Authorization', sprintf('Bearer %s',$this->apiKey));
		$oClient->setContentType('application/json');
		$oClient->setRequestContent(json_encode($jsonRequest));
		$oResponse = $oClient->run();

		if ($oResponse->getStatusCode() < 200 || $oResponse->getStatusCode() >= 300) {
			Logger::error('MailerSender_SendGrid::sendTemplate', null, array(
				'statusCode' => $oResponse->getStatusCode(),
				'contentType' => $oResponse->getContentType(),
				'contentData' => $oResponse->getContentString(),
			));
			throw new IOException(sprintf('SendGrid API response statusCode=%d', $oResponse->getStatusCode()));
		}
		
		return true;		
	}
	
}

?>