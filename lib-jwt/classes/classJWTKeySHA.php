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


class JWTKeySHA implements IJWTKey {
	
	private $algorithmName = '';
	private $passphrase = '';
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getAlgorithmName() {
		return $this->algorithmName;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	private static function getInternalAlgorithmName($name) {
		if ($name == 'HS256') return 'sha256';
		if ($name == 'HS384') return 'sha384';
		if ($name == 'HS512') return 'sha512';
		throw new JWTException('Invalid algorithm name - ' . $name);
	}
	
	//************************************************************************************ 
	/**
	 * @param string $data
	 * @return string
	 */
	public function computeSignature($data) {
		$sig = hash_hmac(self::getInternalAlgorithmName($this->algorithmName), $data, $this->passphrase, true);
		return UtilsString::base64URLEncode($sig);
	}
	
	//************************************************************************************
	/**
	 * @param string $algorithmName
	 * @param string $data
	 * @param string $signature
	 * @return bool
	 */
	public function verify($algorithmName, $data, $signature) {
		$computed = hash_hmac(self::getInternalAlgorithmName($algorithmName), $data, $this->passphrase, true);
		$computed = UtilsString::base64URLEncode($computed);
		return $computed == $signature;
	}
	
	
	//************************************************************************************
	private function __construct($algorithmName, $passphrase) {
		$this->algorithmName = $algorithmName;
		$this->passphrase = $passphrase;
	}
	
	//************************************************************************************
	/**
	 * @param string $passphrase
	 * @return JWTKeySHA
	 */
	public static function CreateHS256($passphrase) {
		if (!$passphrase) throw new InvalidArgumentException('Empty passphrase');
		return new JWTKeySHA('HS256', $passphrase);
	}

	//************************************************************************************
	/**
	 * @param string $passphrase
	 * @return JWTKeySHA
	 */
	public static function CreateHS384($passphrase) {
		if (!$passphrase) throw new InvalidArgumentException('Empty passphrase');
		return new JWTKeySHA('HS384', $passphrase);
	}	
	
	//************************************************************************************
	/**
	 * @param string $passphrase
	 * @return JWTKeySHA
	 */
	public static function CreateHS512($passphrase) {
		if (!$passphrase) throw new InvalidArgumentException('Empty passphrase');
		return new JWTKeySHA('HS512', $passphrase);
	}	
	
}

?>