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
 * Klasa przenoszaca tresc pliku
 * @author pregusia
 *
 */
class FileData implements JsonSerializable {
	
	private $fileName = '';
	private $mimeType = '';
	
	/**
	 * @var binary
	 */
	private $content = '';

	//************************************************************************************
	public function getFileName() { return $this->fileName; }
	public function setFileName($v) { $this->fileName = $v; }
	
	//************************************************************************************
	public function getMimeType() { return $this->mimeType; }
	public function setMimeType($v) { $this->mimeType = $v; }
	
	//************************************************************************************
	public function getContent() { return $this->content; }
	public function setContent($v) { $this->content = $v; }
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'fileName' => $this->fileName,
			'mimeType' => $this->mimeType,
			'content' => base64_encode($this->content)
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return FileData
	 */
	public static function jsonUnserialize($arr) {
		if ($arr['fileName'] || $arr['content']) {
			$obj = new FileData();
			$obj->fileName = $arr['fileName'];
			$obj->mimeType = $arr['mimeType'];
			$obj->content = @base64_decode($arr['content']);
			return $obj;
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @param string $mimeType
	 * @return FileData
	 */
	public static function CreateFromFile($path, $mimeType) {
		if (!file_exists($path)) return null;
		$obj = new FileData();
		$obj->fileName = basename($path);
		$obj->content = file_get_contents($path);
		$obj->mimeType = $mimeType;
		return $obj;
	}
	
}

?>