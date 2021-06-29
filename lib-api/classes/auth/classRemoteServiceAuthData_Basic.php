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


class RemoteServiceAuthData_Basic implements IRemoteServiceAuthData {

	private $userName = '';
	private $plainPassword = '';
	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getUserName() { return $this->userName; }

	//************************************************************************************
	/**
	 * @return string
	 */
	public function getPlainPassword() { return $this->plainPassword; }
	

	//************************************************************************************
	/**
	 * @param string $userName
	 * @param string $password
	 * @throws InvalidArgumentException
	 */
	public function __construct($userName, $password) {
		$userName = trim($userName);
		$password = trim($password);
		
		if (!$userName) throw new InvalidArgumentException('Empty userName');
		if (!$password) throw new InvalidArgumentException('Empty password');
		
		$this->userName = $userName;
		$this->plainPassword = $password;
	}

	//************************************************************************************
	public function jsonSerialize() {
		return array(
			'type' => 'basic',
			'userName' => $this->userName,
			'plainPassword' => $this->plainPassword	
		);
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function headerSerialize() {
		return sprintf('Basic %s', base64_encode(sprintf('%s:%s', $this->userName, $this->plainPassword)));
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return RemoteServiceAuthData_String
	 */
	public static function jsonUnserialize($val) {
		if (is_array($val)) {
			return new RemoteServiceAuthData_Basic($val['userName'], $val['plainPassword']);
		}
		return null;
	}	
	
}

?>