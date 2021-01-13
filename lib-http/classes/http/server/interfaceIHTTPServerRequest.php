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


interface IHTTPServerRequest {
	
	public function getRemoteAddr();
	public function getRemotePort();
	public function getRequestURI();
	public function getRequestURL();
	
	public function getHost();
	public function isSecure();
	public function getMethod();
	public function getReferer();
	public function getContentType();
	
	/**
	 * @param string $name
	 * @return HTTPRequestFile
	 */
	public function getFile($name);

	public function getRequestContent();
	
	
	/**
	 * @return PropertiesMap
	 */
	public function getPOSTParameters();
	
	public function getPOSTParameter($name);
	public function hasPOSTParameter($name);
	
	/**
	 * @return PropertiesMap
	 */
	public function getGETParameters();
	
	public function getGETParameter($name);
	public function hasGETParameter($name);
	
	/**
	 * @return PropertiesMap
	 */
	public function getHeaders();
	
	/**
	 * @return HTTPCookiesContainer
	 */
	public function getCookies();
	
	/**
	 * @return HTTPSession
	 */
	public function getSession();
	
}

?>