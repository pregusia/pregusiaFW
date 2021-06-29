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

interface IHTTPClientRequest {
	
	/**
	 * @param int $method
	 */
	public function setMethod($method);
	
	/**
	 * @param string $url
	 */
	public function setRequestURL($url);
	
	/**
	 * @param string $type
	 */
	public function setContentType($type);

	/**
	 * @param string $content
	 */
	public function setRequestContent($content);
	
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setPOSTParameter($name, $value);
	
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setGETParameter($name, $value);
	
	/**
	 * @return PropertiesMap
	 */
	public function getHeaders();
	
	/**
	 * @return HTTPCookiesContainer
	 */
	public function getCookies();	
	
	
	/**
	 * @param string $path
	 */
	public function setSSLKeyPath($path);
	
	/**
	 * @param string $path
	 */
	public function setSSLCertPath($path);
	
	/**
	 * @param bool $v
	 */
	public function setSSLVerify($v);
	
	/**
	 * @param bool $v
	 */
	public function setFollowRedirects($v);
	
	/**
	 * @param NameValuePair $oUserAndPassword
	 */
	public function setHTTPAuth($oUserAndPassword);
	
	/**
	 * @param string $iface
	 */
	public function setOutgoingInterface($iface);
	
	
	/**
	 * @param int $timeoutSecs
	 * @return IHTTPClientResponse
	 */
	public function run($timeoutSecs=0);
	
	/**
	 * @param ICallback $oCallback
	 */
	public function runAsync($oCallback);
	
	
}

?>