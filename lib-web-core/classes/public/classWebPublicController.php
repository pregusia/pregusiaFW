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

final class WebPublicController extends WebController {
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @return CodeBaseLibraryResource
	 */
	private function locateResource($path) {
		$arr = array_reverse(CodeBase::getLibraries());
		foreach($arr as $oLibrary) {
			false && $oLibrary = new CodeBaseLibrary();
			
			if ($oLibrary->exists($path)) {
				$p = $oLibrary->realPath($path);
				if (is_file($p)) {
					return $oLibrary->getResource($path);
				}
			}
		}
		
		return null;
	}
	
	//************************************************************************************
	/**
	 * @WebAction pub
	 */
	public function webPub() {
		$base = genLink('pub');
		$url = $this->getHTTPRequest()->getRequestURL();
		
		if (UtilsString::startsWith($url, $base)) {
			$file = substr($url, strlen($base));
			if (strpos($file, '?') !== false) $file = strstr($file, '?', true);
			if (strpos($file, '#') !== false) $file = strstr($file, '#', true);
			$file = trim($file,'/');
			
			$path = array();
			foreach(explode('/', $file) as $p) {
				if ($p == '.') continue;
				if ($p == '..' && $path) {
					array_pop($path);
					continue;
				}
				$path[] = $p;
			}
			
			if ($path) {
				array_unshift($path, 'public');
				
				$oResource = $this->locateResource(implode('/', $path));
				if ($oResource) {
					$ifModifiedSince = $this->getHTTPRequest()->getHeaders()->getOneIgnoringCase('If-Modified-Since');
					return new WebResponseLibraryResource($oResource, $ifModifiedSince);
				}
			}
		}
					
		$oResponse = new WebResponsePlainText('404 Not found');
		$oResponse->setHttpCode(404);
		return $oResponse;
	}
	
}

?>