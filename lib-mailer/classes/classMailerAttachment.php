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

class MailerAttachment {
	
	private $fileName = "";
	private $contentType = '';
	private $contentData = null;
	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFileName() { return $this->fileName; }
	public function setFileName($v) { $this->fileName = $v; }
	
	//************************************************************************************
	/**
	 * @return BinaryData
	 */
	public function getContentData() { return $this->contentData; }
	public function setContentData($v) { $this->contentData = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getContentType() { return $this->contentType; }
	public function setContentType($v) { $this->contentType = $v; }

	//************************************************************************************
	/**
	 * @param string $fileName
	 * @param string $contentType
	 * @param string $contentData
	 * @param BinaryData $contentData
	 */
	public function __construct($fileName, $contentType, $contentData) {
		$fileName = trim($fileName);
		if (!$fileName) throw new InvalidArgumentException('fileName is empty');
		
		$this->fileName = $fileName;
		$this->contentType = $contentType;
		
		if ($contentData instanceof BinaryData) {
			$this->contentData = $contentData;
		}
		elseif (is_string($contentData)) {
			$this->contentData = new BinaryData($contentData);
		}
		else {
			throw new InvalidArgumentException('contentData is not BinaryData nor string');
		}
	}
	
} 


?>