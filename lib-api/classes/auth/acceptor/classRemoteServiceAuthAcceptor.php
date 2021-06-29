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
 * Opisuje akceptowalne klucze do API
 * Sklada sie z lini, kazda linia moze byc jako
 * 
 * 
 *  (a) accept *   									<- wtedy jest akceptowane wszystko
 *  (b) accept anonymous							<- wtedy jest akceptowane anonimowe wywolanie (tj. bez auth-data)
 *  (c) accept string cos_tam_*_lala				<- wtedy jest akceptowany autoryzacja string, pasujaca do podanego 'regex'
 *  (d) accept jwt raw <token>						<- akceptowany jest token JWT, dokladnie 1:1 taki jak podany w <token>
 *  (e) accept jwt HS256 ::key-ref::				<- akceptowany token JWT, podpisany podanym algorytmem i podpisany podanym kluczem
 *  (f) accept jwt RS256 ::key-ref::				<- akceptowany token JWT, podpisany podanym algorytmem i podpisany podanym kluczem prywatnym
 *  (g) accept basic user pass						<- akceptuje autoryzacje user/pass z podanymi danymi
 *  (h) allowIP 1.1.1.0/24							<- ogranicza dostep do podanych IP
 *  
 * 
 *  ::key-ref:: <- to jest lireral, za pomoca ktorego mozna sprecyzowac sobie klucz.
 *  Moze byc jako
 *    $variable_name		<- wtedy bierze to z podanej zmiennej
 *    file:...				<- wtedy bierze z podanego pliku
 *    jakis_text_po_prostu	<- wtedy 1:1 bierze to co podane
 * 
 * 
 *
 */
class RemoteServiceAuthAcceptor {
	
	private $entries = array();
	private $allowedIPs = array();
	
	/**
	 * @var IRemoteServiceAuthAcceptorVariableResolver
	 */
	private $oVariableResolver = null;
	
	
	//************************************************************************************
	/**
	 * @param IRemoteServiceAuthAcceptorVariableResolver $oVariableResolver
	 */
	private function __construct($oVariableResolver=null) {
		if ($oVariableResolver) {
			if (!($oVariableResolver instanceof IRemoteServiceAuthAcceptorVariableResolver)) {
				throw new InvalidArgumentException('oVariableResolver is not IRemoteServiceAuthAcceptorVariableResolver');
			}
			$this->oVariableResolver = $oVariableResolver;
		}
	}
	
	
	//************************************************************************************
	/**
	 * @param IRemoteServiceAuthData $oData
	 * @return bool
	 */
	public function isAccepted($oData) {
		if ($this->isAnyAccepted()) return true;
		
		if ($oData) {
			if (!($oData instanceof IRemoteServiceAuthData)) throw new InvalidArgumentException('oData is not IRemoteServiceAuthData');
			
			if ($oData instanceof RemoteServiceAuthData_String) {
				return $this->isStringAccepted($oData->getValue());
			}
			elseif ($oData instanceof RemoteServiceAuthData_JWTToken) {
				return $this->isJWTAccepted($oData);
			}
			elseif ($oData instanceof RemoteServiceAuthData_Basic) {
				return $this->isBasicAccepted($oData);
			}
			else {
				// nie wiemy co to
				return false;
			}
			
		} else {
			// puste, wiec akceptowane jest, kiedy jest * albo anonymous
			return $this->isAnonymousAccepted();
		}
	}	

	//************************************************************************************
	/**
	 * @param string $ipAddress
	 * @return bool
	 */
	public function isIPAccepted($ipAddress) {
		if (count($this->allowedIPs) == 0) return true;
		
		UtilsNet::validateIPv4Address($ipAddress);
		
		foreach($this->allowedIPs as $e) {
			if (UtilsNet::CIDRMatch($ipAddress, $e)) {
				return true;
			}
		}
		
		return false;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isAnyAccepted() {
		foreach($this->entries as $e) {
			if ($e['type'] == 'any') return true;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isAnonymousAccepted() {
		foreach($this->entries as $e) {
			if ($e['type'] == 'any') return true;
			if ($e['type'] == 'anonymous') return true;
		}
		return false;
	}	
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @return bool
	 */
	public function isStringAccepted($value) {
		$value = trim($value);
		if (!$value) return false;
		
		foreach($this->entries as $e) {
			if ($e['type'] == 'string.raw') {
				if ($e['value'] == $value) {
					return true;
				} 
					
			}
			if ($e['type'] == 'string.pattern') {
				if (preg_match($e['pattern'], $value)) {
					return true;
				}
			}
		}
		
		return false;
	}	
	
	//************************************************************************************
	/**
	 * @param RemoteServiceAuthData_Basic $oData
	 * @return bool
	 */
	public function isBasicAccepted($oData) {
		if (!$oData) return false;
		if (!($oData instanceof RemoteServiceAuthData_Basic)) throw new InvalidArgumentException('oData is not RemoteServiceAuthData_Basic');
		
		foreach($this->entries as $e) {
			if ($e['type'] == 'basic') {
				
				if ($e['user'] == $oData->getUserName() && $e['pass'] = $oData->getPlainPassword()) {
					return true;
				}
			}
		}
		
		return false;
	}	
	
	//************************************************************************************
	/**
	 * @param RemoteServiceAuthData_JWTToken $oData
	 */
	public function isJWTAccepted($oData) {
		if (!$oData) return false;
		if (!($oData instanceof RemoteServiceAuthData_JWTToken)) throw new InvalidArgumentException('oData is not RemoteServiceAuthData_JWTToken');
		
		// ok, zeby to sie udalo bedzie nam potrzebne lib-jwt
		// ale to i tak bedzie wyjatek itp, wiec olewamy
		try {
			// ok, pierw sprawdzamy RAW bo nie trzeba wtedy parsowac itp
			foreach($this->entries as $e) {
				if ($e['type'] == 'jwt.raw') {
					if ($e['value'] == $oData->getToken()) {
						return true;
					}
				}
			}
			
			// teraz sprawdzamy inne, bo juz parsujemy
			$oToken = null;
			
			foreach($this->entries as $e) {
				if ($e['type'] == 'jwt.key') {
					if (!$oToken) {
						$oToken = $oData->getToken();
					}
					
					if ($oToken->getHeader()->getAlgorithm() != $e['algo']) continue;
					$oKey = $e['key'];
					
					// TODO: leeway i maxAge do jakiegos configa chyba
					if ($oToken->verifyTimestamp(-1, 30)) {
						if ($oToken->verifyKey($oKey)) {
							return true;
						}
					}
				}
			}
			
		} catch(Exception $e) {
			var_dump($e);
		}
		
		return false;
	}	
	

	
	
	
	
	
	
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @return string
	 */
	private static function preparePattern($value) {
		$pattern = '/^';
		$addWildcard = false;
		foreach(explode('*',$value) as $e) {
			
			if ($addWildcard) {
				$pattern .= '.*';
			}
			
			$pattern .= preg_quote($e);
			$addWildcard = true;
		}
		
		$pattern .= '$/';
		
		return $pattern;
	}
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @return bool
	 */
	private static function isJWTAlgorithmSupported($value) {
		switch($value) {
			case 'RS256':
			case 'RS384':
			case 'RS512':
			case 'HS256':
			case 'HS384':
			case 'HS512':
				return true;
				
			default:
				return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $ref
	 * @return IJWTKey
	 */
	private function parseJWTKeyRef($algorithmName, $ref) {
		$ref = trim($ref);
		if (!$ref) throw new InvalidArgumentException('Empty keyRef (before resolving)');

		if (UtilsString::startsWith($ref, '$')) {
			// variable
			if (!$this->oVariableResolver) {
				throw new IllegalStateException('Need IRemoteServiceAuthAcceptorVariableResolver for variable resolving');
			}
			$ref = $this->oVariableResolver->resolveVariable(ltrim($ref,'$'));
		}
		
		if (UtilsString::startsWith($ref, 'file:')) {
			// file
			$path = substr($ref,5);
			if (file_exists($path)) {
				$ref = file_get_contents($path);
			} else {
				throw new IOException(sprintf('Cannot find file %s', $path));
			}
		}
		
		if (!$ref) throw new InvalidArgumentException('Empty keyRef (after resolving)');
		
		if ($algorithmName == 'HS256') return JWTKeySHA::CreateHS256($ref);
		if ($algorithmName == 'HS384') return JWTKeySHA::CreateHS384($ref);
		if ($algorithmName == 'HS512') return JWTKeySHA::CreateHS512($ref);

		if (UtilsString::startsWith($algorithmName, 'RS')) {
			// zakladamy, ze do weryfikowania uzywamy zawsze klucza publicznego
			// bo w sumie z prywatnym to nie ma zbytnio sensu...

			$oKey = OpenSSLKey::LoadPublicKey($ref);

			if ($algorithmName == 'RS256') return JWTKeyRSA::CreateRS256($oKey);
			if ($algorithmName == 'RS384') return JWTKeyRSA::CreateRS384($oKey);
			if ($algorithmName == 'RS512') return JWTKeyRSA::CreateRS512($oKey);
		}
		
		throw new IllegalStateException(sprintf('Cannot resolve algorithm %s', $algorithmName));
	}
	
	//************************************************************************************
	/**
	 * @param string $line
	 * @return bool
	 */
	private function parseLine($line) {
		$line = trim($line);
		if (!$line) return false;
		
		if ($line == 'accept *') {
			$this->entries[] = array(
				'type' => 'any'	
			);
			return true;
		}
		if ($line == 'accept anonymous') {
			$this->entries[] = array(
				'type' => 'anonymous'	
			);
			return true;
		}
		
		$arr = explode(' ',$line);
		if ($arr[0] == 'accept' && $arr[1] == 'string' && count($arr) == 3) {
			// accept string .....
			
			$val = trim($arr[2]);
			if (strpos($val, '*') !== false) {
				$this->entries[] = array(
					'type' => 'string.pattern',
					'pattern' => self::preparePattern($val)	
				);
			} else {
				$this->entries[] = array(
					'type' => 'string.raw',
					'value' => $val,
				);
			}
			
			return true;			
		}
		if ($arr[0] == 'accept' && $arr[1] == 'basic' && count($arr) == 4) {
			// accept basic user pass
			
			$this->entries[] = array(
				'type' => 'basic',
				'user' => $arr[2],
				'pass' => $arr[3]
			);
			return true;			
		}
		if ($arr[0] == 'accept' && $arr[1] == 'jwt' && count($arr) == 4) {
			// accept jwt HS512 ::key-ref::
			// accept jwt RS256 ::key-ref::
			// ...
			
			if ($arr[2] == 'raw') {
				$this->entries[] = array(
					'type' => 'jwt.raw',
					'value' => $arr[4],
				);
			} else {
				if (!CodeBase::hasLibrary('lib-jwt')) {
					throw new RequirementException('Cannot use jwt with key in RemoteServiceAuthAcceptor without lib-jwt');
				}
				
				$algorithmName = trim($arr[2]);
				if (!self::isJWTAlgorithmSupported($algorithmName)) {
					throw new InvalidArgumentException(sprintf('JWT algorithm "%s" is not supported', $algorithmName));
				} 
				
				$this->entries[] = array(
					'type' => 'jwt.key',
					'algo' => $algorithmName,
					'key' => $this->parseJWTKeyRef($algorithmName, $arr[3])
				);
			}
			return true;
		}
		if ($arr[0] == 'allowIP' && count($arr) == 2) {
			$this->allowedIPs[] = UtilsNet::ensureSubnet($arr[1]);
			return true;
		}
		
		return false;
	}
	

	
	
	
	
	
	
	//************************************************************************************
	/**
	 * @param IRemoteServiceAuthAcceptorVariableResolver $oVariableResolver
	 * @param string $script
	 * @return RemoteServiceAuthAcceptor
	 */
	public static function CreateFromScript($script, $oVariableResolver=null) {
		$obj = new RemoteServiceAuthAcceptor($oVariableResolver);
		
		foreach(explode("\n", $script) as $line) {
			$line = trim($line);
			if (!$line) continue;
			if (UtilsString::startsWith($line, '#')) continue;
			
			$obj->parseLine($line);
		}
		
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param IRemoteServiceAuthAcceptorVariableResolver $oVariableResolver
	 * @param string[] $json
	 * @return RemoteServiceAuthAcceptor
	 */
	public static function CreateFromJSON($json, $oVariableResolver=null) {
		if (!is_array($json)) throw new InvalidArgumentException('json is not array');
		
		foreach($json as $e) {
			if (!is_string($e)) {
				throw new InvalidArgumentException('Given json array element is not string');
			}
		}
		
		$obj = new RemoteServiceAuthAcceptor($oVariableResolver);
		foreach($json as $e) {
			$obj->parseLine($e);
		}
		
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param IRemoteServiceAuthAcceptorVariableResolver $oVariableResolver
	 * @return RemoteServiceAuthAcceptor
	 */
	public static function CreateAcceptingAll($oVariableResolver=null) {
		$obj = new RemoteServiceAuthAcceptor($oVariableResolver);
		$obj->parseLine('accept *');
		return $obj;
	}
	
}

?>