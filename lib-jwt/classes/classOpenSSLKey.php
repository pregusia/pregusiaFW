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


class OpenSSLKey {
	
	const TYPE_PRIVATE = 1;
	const TYPE_PUBLIC = 2;

	const MODE_FILE = 1;
	const MODE_CONTENT = 2;
	
	private $type = 1;
	private $mode = 1;
	private $path = '';
	private $content = '';
	private $password = '';
	
	//************************************************************************************
	private function __construct($type, $mode, $value, $password) {
		$this->type = $type;
		$this->mode = $mode;
		$this->password = $password;
		
		if ($mode == self::MODE_CONTENT) {
			$value = trim($value);
			if (!$value) throw new InvalidArgumentException('Empty content');
			$this->content = $value;
		}
		elseif ($mode == self::MODE_FILE) {
			if (!$value) throw new InvalidArgumentException('Empty path');
			if (!is_file($value)) throw new IOException('Cannot read ' . $value);
			$this->path = $value;
		}
		else {
			throw new InvalidArgumentException('Invalid mode');
		}
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	private function getContent() {
		if ($this->mode == self::MODE_CONTENT) {
			return $this->content;
		}
		if ($this->mode == self::MODE_FILE) {
			return file_get_contents($this->path);
		}
		throw new IllegalStateException('Invalid mode');
	}
	
	//************************************************************************************
	/**
	 * @return resource
	 */
	public function readKey() {
		if ($this->type == self::TYPE_PRIVATE) {
			return openssl_get_privatekey($this->getContent(), $this->password);
		}
		if ($this->type == self::TYPE_PUBLIC) {
			return openssl_get_publickey($this->getContent());
		}
		throw new IllegalStateException('Invalid key type');
	}
	
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @param string $password
	 * @return OpenSSLKey
	 */
	public static function LoadPrivateKeyFromFile($path, $password) {
		return new OpenSSLKey(self::TYPE_PRIVATE, self::MODE_FILE, $path, $password);
	}

	//************************************************************************************
	/**
	 * @param string $content
	 * @param string $password
	 * @return OpenSSLKey
	 */
	public static function LoadPrivateKey($content, $password) {
		return new OpenSSLKey(self::TYPE_PRIVATE, self::MODE_CONTENT, $content, $password);
	}	
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @return OpenSSLKey
	 */
	public static function LoadPublicKeyFromFile($path) {
		return new OpenSSLKey(self::TYPE_PUBLIC, self::MODE_FILE, $path, '');
	}

	//************************************************************************************
	/**
	 * @param string $content
	 * @return OpenSSLKey
	 */
	public static function LoadPublicKey($content) {
		return new OpenSSLKey(self::TYPE_PUBLIC, self::MODE_CONTENT, $content, '');
	}
	
	
}

?>