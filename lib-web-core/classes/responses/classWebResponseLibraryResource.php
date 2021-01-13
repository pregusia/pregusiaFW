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

class WebResponseLibraryResource extends WebResponseBase {
	
	private $oResource = null;
	private $ifModifiedSince = '';
	
	//************************************************************************************
	/**
	 * @return CodeBaseLibraryResource
	 */
	public function getResource() { return $this->oResource; }
	
	//************************************************************************************
	/**
	 * @param unknown $oResource
	 */
	public function __construct($oResource, $ifModifiedSince='') {
		if (!($oResource instanceof CodeBaseLibraryResource)) throw new InvalidArgumentException('oResource is not CodeBaseLibraryResource');
		$this->oResource = $oResource;
		$this->ifModifiedSince = $ifModifiedSince;
	}
	
	//************************************************************************************
	/**
	 * @param CodeBaseLibraryResource $oResource
	 * @return string
	 */
	public static function getMimeType($oResource) {
		if (!($oResource instanceof CodeBaseLibraryResource)) throw new InvalidArgumentException('oResource is not CodeBaseLibraryResource');
		
		if (UtilsString::endsWith($oResource->getName(), '.php')) return '';
		
		if (UtilsString::endsWith($oResource->getName(), '.png')) return 'image/png';
		if (UtilsString::endsWith($oResource->getName(), '.gif')) return 'image/gif';
		if (UtilsString::endsWith($oResource->getName(), '.ico')) return 'image/vnd.microsoft.icon';
		if (UtilsString::endsWith($oResource->getName(), '.jpg')) return 'image/jpeg';
		if (UtilsString::endsWith($oResource->getName(), '.jpeg')) return 'image/jpeg';
		if (UtilsString::endsWith($oResource->getName(), '.svg')) return 'image/svg+xml';
		
		if (UtilsString::endsWith($oResource->getName(), '.css')) return 'text/css';
		if (UtilsString::endsWith($oResource->getName(), '.js')) return 'application/javascript';
		if (UtilsString::endsWith($oResource->getName(), '.json')) return 'application/json';
		if (UtilsString::endsWith($oResource->getName(), '.xml')) return 'text/xml';
		if (UtilsString::endsWith($oResource->getName(), '.htm')) return 'text/html';
		if (UtilsString::endsWith($oResource->getName(), '.html')) return 'text/html';
		if (UtilsString::endsWith($oResource->getName(), '.txt')) return 'text/plain';
		if (UtilsString::endsWith($oResource->getName(), '.csv')) return 'text/csv';
		
		if (UtilsString::endsWith($oResource->getName(), '.eot')) return 'font/eot';
		if (UtilsString::endsWith($oResource->getName(), '.ttf')) return 'font/ttf';
		if (UtilsString::endsWith($oResource->getName(), '.woff')) return 'font/woff';
		if (UtilsString::endsWith($oResource->getName(), '.woff2')) return 'font/woff2';
		
		if (UtilsString::endsWith($oResource->getName(), '.swf')) return 'application/x-shockwave-flash';
		if (UtilsString::endsWith($oResource->getName(), '.pdf')) return 'application/pdf';
		
		if (UtilsString::endsWith($oResource->getName(), '.gz')) return 'application/gzip';
		if (UtilsString::endsWith($oResource->getName(), '.zip')) return 'application/zip';
		if (UtilsString::endsWith($oResource->getName(), '.rar')) return 'application/rar';
		if (UtilsString::endsWith($oResource->getName(), '.7z')) return 'application/x-7z-compressed';
		if (UtilsString::endsWith($oResource->getName(), '.tar')) return 'application/x-tar';
		
		return 'application/octet-stream';
	}	
	
	//************************************************************************************
	/**
	 * @param IHTTPServerResponse $oHttpResponse
	 */
	public function finish($oHttpResponse) {
		$mimeType = self::getMimeType($this->getResource());
		if ($mimeType) {
			$fileModificationTime = gmdate('D, d M Y H:i:s', $this->getResource()->mtime()).' GMT';
			if ($this->ifModifiedSince && $this->ifModifiedSince == $fileModificationTime) {
				// not changed
				$oHttpResponse->getHeaders()->putSingle('Last-Modified', $fileModificationTime);
				$oHttpResponse->getHeaders()->putSingle('Cache-Control', 'public');
				$oHttpResponse->getHeaders()->putSingle('Expires', '');
				$oHttpResponse->setStatusCode(304);
				return;
			}
			
			$self = $this;
			$oHttpResponse->setContentType($mimeType);
			$oHttpResponse->getHeaders()->putSingle('Last-Modified', $fileModificationTime);
			$oHttpResponse->getHeaders()->putSingle('Cache-Control', 'public');
			$oHttpResponse->getHeaders()->putSingle('Expires', '');
			$oHttpResponse->pushOutputFunction(function() use ($self){
				readfile($self->getResource()->realPath());
			});			
		} else {
			// file with this ext is forbidden
			$oHttpResponse->setStatusCode(404);
			$oHttpResponse->pushOutputFunction(function() use ($self){
				print('404 Not found');
			});
		}
	}	
	
}

?>