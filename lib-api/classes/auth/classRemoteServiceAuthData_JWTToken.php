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
 *  
 *  Based on
 *   https://github.com/firebase/php-jwt/blob/master/src/JWT.php
 *  
 *
 */

class RemoteServiceAuthData_JWTToken implements IRemoteServiceAuthData {
	
	private $tokenRaw = '';
	
	/**
	 * @var JWT
	 */
	private $oToken = null;
	

	//************************************************************************************
	public function getTokenString() {
		return $this->tokenRaw;
	}
	
	//************************************************************************************
	/**
	 * @return JWT
	 */
	public function getToken() {
		CodeBase::ensureLibrary('lib-jwt', 'lib-api');
		return $this->oToken;
	}
	
	//************************************************************************************
	/**
	 * @param string $token
	 */
	public function __construct($token) {
		$token = trim($token);
		if (!$token) throw new InvalidArgumentException('Empty token');
		
		$this->tokenRaw = $token;
		if (CodeBase::hasClass('JWT')) {
			$this->oToken = JWT::UnserializeToken($token);
		}
	}
	
	//************************************************************************************
	/**
	 * @param int $maxAge
	 * @param int $leeway
	 * @param int $now
	 */	
	public function verifyTimestamp($maxAge, $leeway, $now=false) {
		return $this->getToken()->verifyTimestamp($maxAge, $leeway, $now);
	}
	
	//************************************************************************************
	/**
	 * @param IJWTKey $oKey
	 */
	public function verifyKey($oKey) {
		return $this->getToken()->verifyKey($oKey);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'type' => 'JWTToken',
			'token' => $this->tokenRaw
		);
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function headerSerialize() {
		return sprintf('Bearer %s', $this->tokenRaw);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return RemoteServiceAuthData_JWTToken
	 */
	public static function jsonUnserialize($arr) {
		if (is_array($arr)) {
			if (!$arr['token']) return null;
			return new RemoteServiceAuthData_JWTToken($arr['token']);
		}
		return null;
	}
	
		
}

?>