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


class JWTKeyRSA implements IJWTKey {
	
	private $algorithmName = '';
	
	/**
	 * @var OpenSSLKey
	 */
	private $keyFile = null;
	
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
		if ($name == 'RS256') return OPENSSL_ALGO_SHA256;
		if ($name == 'RS384') return OPENSSL_ALGO_SHA384;
		if ($name == 'RS512') return OPENSSL_ALGO_SHA512;
		throw new JWTException('Invalid algorithm name - ' . $name);
	}	
	
	
	//************************************************************************************ 
	/**
	 * @param string $data
	 * @return string
	 */
	public function computeSignature($data) {
		$keyResource = $this->keyFile->readKey();
		
		$signature = '';
		openssl_sign($data, $signature, $keyResource, self::getInternalAlgorithmName($this->algorithmName));

		return UtilsString::base64URLEncode($signature);		
	}
	
	//************************************************************************************
	/**
	 * @param string $algorithmName
	 * @param string $data
	 * @param string $signature
	 * @return bool
	 */
	public function verify($algorithmName, $data, $signature) {
		$signatureRaw = UtilsString::base64URLDecode($signature);
		$keyResource = $this->keyFile->readKey();
		
		return openssl_verify($data, $signatureRaw, $keyResource, self::getInternalAlgorithmName($algorithmName)) === 1;
	}
	
	
	//************************************************************************************
	private function __construct($algorithmName, $keyFile) {
		if (!($keyFile instanceof OpenSSLKey)) throw new InvalidArgumentException('key is not OpenSSLKey');
		
		$this->algorithmName = $algorithmName;
		$this->keyFile = $keyFile;
	}
	
	//************************************************************************************
	/**
	 * @param OpenSSLKey $keyFile
	 * @return JWTKeyRSA
	 */
	public static function CreateRS256($keyFile) {
		return new JWTKeyRSA('RS256', $keyFile);
	}

	//************************************************************************************
	/**
	 * @param OpenSSLKey $keyFile
	 * @return JWTKeyRSA
	 */
	public static function CreateRS384($keyFile) {
		return new JWTKeyRSA('RS384', $keyFile);
	}
	
	//************************************************************************************
	/**
	 * @param OpenSSLKey $keyFile
	 * @return JWTKeyRSA
	 */
	public static function CreateRS512($keyFile) {
		return new JWTKeyRSA('RS512', $keyFile);
	}		
	
}

?>