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
 * Dane przechowywane w JsonWebToken
 * @author pregusia
 *
 */
class JWT implements JsonSerializable {
	
	
	/**
	 * @var JWTHeader
	 */
	private $oHeader = null;
	
	/**
	 * @var JWTPayload
	 */
	private $oPayload = null;
	
	/**
	 * Sygnatura (base64url)
	 * 
	 * @var string
	 */
	private $signature = '';
	
	
	//************************************************************************************
	/**
	 * @return IJWTKey
	 */
	public function getKey() { return $this->key; }
	public function setKey($v) { $this->key = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getSignature() { return $this->signature; }

	//************************************************************************************
	/**
	 * @return JWTHeader
	 */
	public function getHeader() { return $this->oHeader; }

	//************************************************************************************
	/**
	 * @return JWTPayload
	 */
	public function getPayload() { return $this->oPayload; }
	
	
	//************************************************************************************
	public function __construct() {
		$this->oHeader = new JWTHeader();
		$this->oPayload = new JWTPayload();
	}
	
	//************************************************************************************
	/**
	 * @param int $maxAge
	 * @param int $leeway
	 * @param int $now
	 */
	public function verifyTimestamp($maxAge, $leeway, $now=false) {
		if (!$now) {
			$now = ApplicationContext::getCurrent()->getTimestamp();
		}
		return $this->getPayload()->verifyTimestamp($now, $maxAge, $leeway);
	}
	
	//************************************************************************************
	/**
	 * @param IJWTKey $oKey
	 */
	public function verifyKey($oKey) {
		if (!($oKey instanceof IJWTKey)) throw new InvalidArgumentException('oKey is not IJWTKey');
		
		$header = $this->getHeader()->serialize();
		$payload = $this->getPayload()->serialize();
		
		return $oKey->verify($this->getHeader()->getAlgorithm(), sprintf('%s.%s', $header, $payload), $this->signature);
	}
	
	//************************************************************************************
	/**
	 * @param IJWTKey $oKey
	 * @return string
	 */
	public function serialize($oKey) {
		if (!($oKey instanceof IJWTKey)) throw new InvalidArgumentException('oKey is not IJWTKey');
		
		$oHeader = $this->getHeader()->getCopy();
		$oHeader->set(JWTHeader::FIELD_TOKEN_TYPE, 'jwt');
		$oHeader->set(JWTHeader::FIELD_ALGORITHM, $oKey->getAlgorithmName());
		
		$header = $oHeader->serialize();
		$payload = $this->getPayload()->serialize();
		$signature = $oKey->computeSignature($header . '.' . $payload);

		return $header . '.' . $payload . '.' . $signature;		
	}
	
	//************************************************************************************
	/**
	 * @param string $header
	 * @param bool $verify
	 * @return JWT
	 */
	public static function UnserializeToken($token) {
		if (substr_count($token, '.') < 2) throw new JWTException('Invalid token: Incomplete segments');

		$token = explode('.', $token, 3);
		
		$obj = new JWT();
		$obj->oHeader = JWTHeader::UnserializeTokenPart($token[0]);
		$obj->oPayload = JWTPayload::UnserializeTokenPart($token[1]);
		$obj->signature = $token[2];
		
		return $obj;
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'header' => $this->getHeader()->jsonSerialize(),
			'payload' => $this->getPayload()->jsonSerialize(),
			'signature' => $this->signature,
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return JWT
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr['header'] && $arr['payload']) {
			$obj = new JWT();
			$obj->signature = strval($arr['signature']);
			$obj->oHeader = JWTHeader::jsonUnserialize($arr['header']);
			$obj->oPayload = JWTPayload::jsonUnserialize($arr['payload']);
			return $obj;
		}
		return null;
	}
	
	
}

?>